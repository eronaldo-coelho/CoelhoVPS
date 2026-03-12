<?php

namespace App\Http\Controllers;

use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
// Imports do SDK do Mercado Pago
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;

class WebhookController extends Controller
{
    public function handleMercadoPago(Request $request)
    {
        // 1. Log Imediato: Salva tudo o que recebemos para depuração futura.
        Log::channel('daily')->info('Mercado Pago Webhook Recebido:', $request->all());

        // 2. Validação Básica: Verifica se a notificação é do tipo 'payment'.
        if ($request->input('type') !== 'payment' || !$request->has('data.id')) {
            Log::channel('daily')->warning('Webhook recebido não é do tipo "payment" ou não contém "data.id".');
            return response()->json(['status' => 'error', 'message' => 'Invalid payload'], 400);
        }

        try {
            $paymentId = $request->input('data.id');

            // 3. Busca o pagamento em nosso banco de dados.
            $pagamentoLocal = Pagamento::where('payment_id_gateway', $paymentId)->first();

            if (!$pagamentoLocal) {
                Log::channel('daily')->error("Pagamento com ID do Gateway [{$paymentId}] não encontrado no banco de dados.");
                return response()->json(['status' => 'not_found'], 404);
            }

            // Não confia no status do webhook, busca a informação mais recente na API
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
            $client = new PaymentClient();
            $paymentInfoApi = $client->get($paymentId);
            Log::channel('daily')->warning($paymentInfoApi->status);
            // 4. Se o pagamento foi aprovado E nosso status ainda está pendente
            if ($paymentInfoApi->status === 'approved' && $pagamentoLocal->status === 'pending') {
                
                // Atualiza a tabela 'pagamentos'
                $pagamentoLocal->status = 'approved'; // ou 'pago'
                $pagamentoLocal->data_pagamento = now();
                $pagamentoLocal->save();

                // Atualiza a tabela 'contratos'
                $contrato = $pagamentoLocal->contrato;
                if ($contrato->status === 'pendente') {
                    $contrato->status = 'configurando'; // ou 'configurando'
                    $contrato->save();
                }

                Log::channel('daily')->info("Pagamento [{$paymentId}] APROVADO. Status do contrato #{$contrato->id} atualizado para ATIVO.");
                Log::info("Pagamento aprovado. Acionando provisionamento para contrato #{$contrato->id}");
                Http::post(route('api.provision.instance', ['contrato' => $contrato->id]));

            } else {
                // Opcional: Atualiza o status para outros casos (cancelado, recusado, etc.)
                $pagamentoLocal->status = $paymentInfoApi->status;
                $pagamentoLocal->save();

                Log::channel('daily')->info("Status do pagamento [{$paymentId}] atualizado para '{$paymentInfoApi->status}'. Nenhuma ação no contrato foi necessária.");
            }

        } catch (MPApiException $e) {
            Log::channel('daily')->critical("Erro na API do Mercado Pago ao processar webhook: " . $e->getApiResponse()->getContent()['message'], ['payment_id' => $paymentId ?? null]);
            return response()->json(['status' => 'api_error'], 500);
        } catch (\Exception $e) {
            Log::channel('daily')->critical("Erro genérico ao processar webhook: " . $e->getMessage(), ['payment_id' => $paymentId ?? null]);
            return response()->json(['status' => 'internal_error'], 500);
        }

        // 5. Responde ao Mercado Pago com status 200 OK para confirmar o recebimento.
        return response()->json(['status' => 'received'], 200);
    }
}