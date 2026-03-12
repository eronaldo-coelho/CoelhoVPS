<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\Pagamento;
use Illuminate\Http\Request;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Exceptions\MPApiException;

class PagamentoController extends Controller
{
    public function exibir(Contrato $contrato)
    {
        if (auth()->id() !== $contrato->user_id) {
            abort(403, 'Acesso não autorizado.');
        }

        if ($contrato->status !== 'pendente' && $contrato->status !== 'suspenso' && $contrato->status !== 'ativo') {
            return redirect()->route('painel.dashboard')->with('info', 'Este contrato já foi ativado.');
        }

        $pagamentoExistente = Pagamento::where('contrato_id', $contrato->id)
            ->where('status', 'pending')
            ->where('data_vencimento', '>', now())
            ->latest()
            ->first();

        if ($pagamentoExistente) {
            return view('pagamento.pix', ['pagamento' => $pagamentoExistente]);
        }

        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
            $client = new PaymentClient();

            $expirationDate = now()->addDays(30)->format('Y-m-d\TH:i:s.vP');
            
            $request = [
                "transaction_amount" => (float) $contrato->valor_total_mensal,
                "description" => "Servidor VPS - Contrato #" . $contrato->id,
                "payment_method_id" => "pix",
                "date_of_expiration" => $expirationDate,
                "payer" => [
                    "email" => $contrato->user->email,
                    "first_name" => strtok($contrato->user->name, ' '),
                    "last_name" => strstr($contrato->user->name, ' ') ?: $contrato->user->name,
                ],
            ];

            $payment = $client->create($request);

            $novoPagamento = Pagamento::create([
                'user_id' => auth()->id(),
                'contrato_id' => $contrato->id,
                'payment_id_gateway' => $payment->id,
                'status' => 'pending',
                'valor' => $contrato->valor_total_mensal,
                'qr_code_base64' => $payment->point_of_interaction->transaction_data->qr_code_base64,
                'qr_code_text' => $payment->point_of_interaction->transaction_data->qr_code,
                'data_vencimento' => $expirationDate,
            ]);

            return view('pagamento.pix', ['pagamento' => $novoPagamento]);

        } catch (MPApiException $e) {
            return redirect()->route('dashboard')->withErrors('API Error: ' . $e->getApiResponse()->getContent()['message']);
        } catch (\Exception $e) {
            return redirect()->route('dashboard')->withErrors('Erro: ' . $e->getMessage());
        }
    }
}