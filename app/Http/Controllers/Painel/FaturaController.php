<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Customer\CustomerCardClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

class FaturaController extends Controller
{
    public function index()
    {
        $usuario = Auth::user();
        $usuarioId = $usuario->id;

        $faturasPendentes = Pagamento::where('user_id', $usuarioId)
                                     ->where('status', 'pending')
                                     ->with('contrato')
                                     ->latest()
                                     ->get();

        $faturasPagas = Pagamento::where('user_id', $usuarioId)
                                 ->where('status', 'approved')
                                 ->with('contrato')
                                 ->latest()
                                 ->get();

        $cards = collect();
        $cliente = Cliente::where('user_id', $usuarioId)->first();

        if ($cliente && $cliente->customer_id_gateway) {
            try {
                MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
                $card_client = new CustomerCardClient();
                $customerCardsResult = $card_client->list($cliente->customer_id_gateway);
                $cards = collect($customerCardsResult->data ?? []);
            } catch (\Exception $e) {
                // Silently fail
            }
        }

        return view('painel.faturas.index', compact('faturasPendentes', 'faturasPagas', 'cards'));
    }

    public function processarPagamentoCartao(Request $request)
    {
        $request->validate([
            'pagamento_id' => 'required|exists:pagamentos,id',
            'card_id' => 'required|string',
            'payment_token' => 'required|string',
            'payment_method_id' => 'required|string',
        ]);

        $pagamento = Pagamento::with('contrato', 'user.cliente')->findOrFail($request->pagamento_id);

        if ($pagamento->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        if ($pagamento->status !== 'pending') {
            return redirect()->route('painel.faturas.index')->with('error', 'Esta fatura não está mais pendente.');
        }

        $cliente = $pagamento->user->cliente;
        if (!$cliente || !$cliente->identification_number) {
            return redirect()->route('painel.faturas.index')->with('error', 'Seu cadastro precisa estar completo para pagar com cartão.');
        }

        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
            $payment_client = new PaymentClient();
            $request_options = new RequestOptions();
            $request_options->setCustomHeaders(["X-Idempotency-Key: " . Uuid::uuid4()->toString()]);

            $documentoLimpo = preg_replace('/\D/', '', $cliente->identification_number);
            $tipoDocumento = strlen($documentoLimpo) == 11 ? 'CPF' : 'CNPJ';

            $payment_data = [
                "transaction_amount" => (float) round($pagamento->valor, 2),
                "token" => $request->payment_token,
                "description" => "Pagamento Fatura #" . $pagamento->id . " - Contrato #" . $pagamento->contrato_id,
                "installments" => 1,
                "payment_method_id" => $request->payment_method_id,
                "payer" => [
                    "type" => "customer",
                    "id" => $cliente->customer_id_gateway,
                    "email" => $pagamento->user->email,
                    "identification" => ["type" => $tipoDocumento, "number" => $documentoLimpo]
                ]
            ];

            $payment = $payment_client->create($payment_data, $request_options);

            if ($payment->status === 'approved') {
                $pagamento->update([
                    'status' => 'approved',
                    'tipo_pagamento' => Pagamento::TIPO_CARTAO,
                    'status_detalhe' => $payment->status_detail,
                    'metodo_pagamento' => $payment->payment_method_id,
                    'card_last_four' => $payment->card?->last_four_digits,
                    'parcelas' => $payment->installments,
                    'data_pagamento' => Carbon::parse($payment->date_approved),
                ]);

                $proximaData = Carbon::parse($pagamento->contrato->data_proximo_vencimento)->addDays(30)->toDateString();
                $pagamento->contrato->update(['status' => 'ativo', 'data_proximo_vencimento' => $proximaData]);

                return redirect()->route('painel.faturas.index')->with('success', 'Fatura #' . $pagamento->id . ' paga com sucesso!');
            } else {
                $pagamento->update(['status_detalhe' => $payment->status_detail]);
                return redirect()->route('painel.faturas.index')->with('error', 'Pagamento recusado pela operadora. Tente novamente ou use outro cartão.');
            }
        } catch (MPApiException $e) {
            Log::error("Erro API MP Fatura #" . $pagamento->id . ": " . json_encode($e->getApiResponse()->getContent()));
            return redirect()->route('painel.faturas.index')->with('error', 'Erro de comunicação com o gateway de pagamento.');
        } catch (\Exception $e) {
            Log::error("Erro Geral Fatura #" . $pagamento->id . ": " . $e->getMessage());
            return redirect()->route('painel.faturas.index')->with('error', 'Ocorreu um erro inesperado. Tente novamente mais tarde.');
        }
    }
}