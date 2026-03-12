<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Contrato;
use App\Models\Pagamento;
use App\Models\InstanciaVps;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;

class GerarFaturasRenovacao extends Command
{
    /**
     * O nome e a assinatura do comando do console.
     */
    protected $signature = 'faturas:gerar-renovacao';

    /**
     * A descrição do comando do console.
     */
    protected $description = 'Sincroniza datas nulas, gera faturas de renovação e suspende contratos vencidos.';

    private $apiUrl = 'https://api.contabo.com';
    private $authUrl = 'https://auth.contabo.com/auth/realms/contabo/protocol/openid-connect/token';

    /**
     * Executa o comando do console.
     */
    public function handle()
    {
        $this->info('Iniciando tarefa agendada de gerenciamento de faturas...');
        Log::info('Tarefa agendada iniciada: GerarFaturasRenovacao');

        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
        } catch (\Exception $e) {
            $this->error('Falha ao configurar o Mercado Pago: ' . $e->getMessage());
            return 1;
        }

        // 1. Primeiro, corrigimos contratos que estão com a data_proximo_vencimento nula
        $this->sincronizarDatasVencimentoNulas();

        // 2. Geramos faturas para quem está próximo do vencimento
        $this->gerarFaturasPendentes();

        // 3. Suspendemos quem já passou do prazo
        $this->suspenderContratosVencidos();

        $this->info('Tarefa concluída com sucesso.');
        return 0;
    }

    /**
     * Lógica para preencher data_proximo_vencimento caso esteja nula.
     */
    private function sincronizarDatasVencimentoNulas()
    {
        $this->info('Sincronizando datas de vencimento nulas...');

        $contratosNulos = Contrato::whereNull('data_proximo_vencimento')->get();

        foreach ($contratosNulos as $contrato) {
            $diaCriacao = $contrato->created_at->day;
            
            // Busca o último pagamento (aprovado ou pendente)
            $ultimoPagamento = Pagamento::where('contrato_id', $contrato->id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($ultimoPagamento) {
                // Se existe pagamento, pegamos o mês/ano desse pagamento e o dia original do contrato
                $mesReferencia = $ultimoPagamento->data_vencimento ?? $ultimoPagamento->created_at;
                $novaData = Carbon::create($mesReferencia->year, $mesReferencia->month, $diaCriacao, 0, 0, 0);
            } else {
                // Se não há nenhum pagamento, o primeiro vencimento é no mês seguinte à criação
                $novaData = $contrato->created_at->copy()->addMonth()->day($diaCriacao);
            }

            $contrato->data_proximo_vencimento = $novaData;
            $contrato->save();

            $this->line("Data preenchida para Contrato #{$contrato->id}: {$novaData->format('d/m/Y')}");
        }
    }

    /**
     * Lógica para gerar novas faturas de renovação via Mercado Pago (PIX).
     */
    private function gerarFaturasPendentes()
    {
        // Gera fatura 7 dias antes de vencer
        $dataLimiteGeracao = Carbon::now()->addDays(7);

        $contratosParaRenovar = Contrato::with('user')
            ->where('status', 'ativo')
            ->whereNotNull('data_proximo_vencimento')
            ->whereDate('data_proximo_vencimento', '<=', $dataLimiteGeracao)
            ->get();

        if ($contratosParaRenovar->isEmpty()) {
            $this->info('Nenhum contrato precisando de nova fatura agora.');
            return;
        }

        $client = new PaymentClient();

        foreach ($contratosParaRenovar as $contrato) {
            // Verifica se já existe uma fatura pendente para este contrato para não duplicar
            $faturaPendenteExistente = Pagamento::where('contrato_id', $contrato->id)
                ->where('status', 'pending')
                ->exists();

            if ($faturaPendenteExistente) {
                continue;
            }

            if (!$contrato->user) continue;

            try {
                // A fatura expira na data do vencimento do contrato
                $expirationDate = $contrato->data_proximo_vencimento->format('Y-m-d\TH:i:s.vP');

                $request = [
                    "transaction_amount" => (float) $contrato->valor_total_mensal,
                    "description" => "Renovação VPS - Contrato #" . $contrato->id,
                    "payment_method_id" => "pix",
                    "date_of_expiration" => $expirationDate,
                    "payer" => [
                        "email" => $contrato->user->email,
                        "first_name" => strtok($contrato->user->name, ' '),
                        "last_name" => strstr($contrato->user->name, ' ') ?: $contrato->user->name,
                    ],
                ];

                $payment = $client->create($request);

                Pagamento::create([
                    'user_id' => $contrato->user_id,
                    'contrato_id' => $contrato->id,
                    'payment_id_gateway' => $payment->id,
                    'status' => 'pending',
                    'tipo_pagamento' => 'pix',
                    'valor' => $contrato->valor_total_mensal,
                    'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                    'qr_code_text' => $payment->point_of_interaction->transaction_data->qr_code,
                    'data_vencimento' => $contrato->data_proximo_vencimento,
                ]);

                $this->info("Fatura gerada para Contrato #{$contrato->id}");

            } catch (\Exception $e) {
                Log::error("Erro ao gerar renovação Contrato #{$contrato->id}: " . $e->getMessage());
            }
        }
    }

    /**
     * Lógica para suspender contratos vencidos e desligar VPS na Contabo.
     */
    private function suspenderContratosVencidos()
    {
        $contratosParaSuspender = Contrato::with('instancia')
            ->where('status', 'ativo')
            ->whereNotNull('data_proximo_vencimento')
            ->whereDate('data_proximo_vencimento', '<', Carbon::now()->startOfDay())
            ->get();
            
        if ($contratosParaSuspender->isEmpty()) return;

        $accessToken = $this->getContaboAccessToken();

        foreach($contratosParaSuspender as $contrato) {
            $contrato->status = 'suspenso';
            $contrato->save();
            
            if ($contrato->instancia && $accessToken) {
                try {
                    $instanceIdContabo = $contrato->instancia->instance_id_contabo;
                    $url = "{$this->apiUrl}/v1/compute/instances/{$instanceIdContabo}/actions/shutdown";
                    
                    $response = Http::withToken($accessToken)
                        ->withHeaders(['x-request-id' => (string) Str::uuid()])
                        ->post($url);

                    if ($response->successful()) {
                        $contrato->instancia->status = 'stopping';
                        $contrato->instancia->save();
                        Log::info("VPS do contrato #{$contrato->id} suspensa (Shutdown enviado).");
                    }
                } catch (\Exception $e) {
                    Log::error("Erro Contabo Contrato #{$contrato->id}: " . $e->getMessage());
                }
            }
        }
    }

    /**
     * Obtém token de acesso da Contabo.
     */
    private function getContaboAccessToken(): ?string
    {
        return Cache::remember('contabo_access_token', 240, function () {
            $response = Http::asForm()->post($this->authUrl, [
                'client_id' => config('services.contabo.client_id'),
                'client_secret' => config('services.contabo.client_secret'),
                'username' => config('services.contabo.api_user'),
                'password' => config('services.contabo.api_password'),
                'grant_type' => 'password',
            ]);

            return $response->successful() ? $response->json('access_token') : null;
        });
    }
}