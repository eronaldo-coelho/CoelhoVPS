<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\Pagamento;
use App\Models\Regiao;
use App\Models\Servidor;
use App\Models\ServidorApi;
use App\Models\Sistema;
use App\Models\ConfiguracaoPendente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use MercadoPago\Client\Common\RequestOptions;
use MercadoPago\Client\Customer\CustomerCardClient;
use MercadoPago\Client\Payment\PaymentClient;
use MercadoPago\MercadoPagoConfig;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Ramsey\Uuid\Uuid;

class CheckoutController extends Controller
{
    public function iniciar(Request $request)
    {
        if ($request->isMethod('post')) {
            ConfiguracaoPendente::updateOrCreate(
                ['session_id' => Session::getId()],
                ['payload' => $request->all(), 'user_id' => Auth::id()]
            );
        }

        $configData = ConfiguracaoPendente::where('user_id', Auth::id())
            ->orWhere('session_id', Session::getId())
            ->first();

        if (!$configData) {
            return redirect('/');
        }

        $config = $configData->payload;

        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();
        
        if (is_null($user->password)) {
            return redirect()->route('senha.definir.mostrar');
        }

        // CORREÇÃO AQUI: Verificando se a relação cliente existe e se os campos corretos estão preenchidos
        if (!$user->cliente || !$user->cliente->identification_number || !$user->cliente->phone_number) {
            return redirect()->route('cliente.create');
        }

        $servidor     = Servidor::findOrFail($config['servidor_id']);
        $opcaoDisco   = ServidorApi::where('product_id', $config['product_id'])->firstOrFail();
        $regiao       = Regiao::where('regiao_id', $config['regiao_id'])->firstOrFail();
        $sistema      = Sistema::where('image_id', $config['sistema_id'])->firstOrFail();
        
        $valorBase = $servidor->valor * (1 - ($servidor->desconto_percentual / 100));
        $custoAdicional = $valorBase * ($regiao->porcentagem / 100);
        $valorTotal = $valorBase + $custoAdicional;

        $cards = collect();
        $cliente = $user->cliente; // Usando a relação já carregada
        
        if ($cliente && $cliente->customer_id_gateway) {
            try {
                MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
                $card_client = new CustomerCardClient();
                $customerCardsResult = $card_client->list($cliente->customer_id_gateway);
                $cards = collect($customerCardsResult->data ?? []);
            } catch (\Exception $e) {
                Log::error($e->getMessage());
            }
        }

        return view('checkout', [
            'usuario' => $user,
            'servidor' => $servidor,
            'opcaoDisco' => $opcaoDisco,
            'regiao' => $regiao,
            'sistema' => $sistema,
            'valorTotal' => $valorTotal,
            'cards' => $cards,
        ]);
    }

    public function processar(Request $request)
    {
        $user = Auth::user();
        $cliente = $user->cliente;
    
        $servidor = Servidor::findOrFail($request->servidor_id);
        $opcaoDisco = ServidorApi::findOrFail($request->servidor_api_id);
        $regiao = Regiao::findOrFail($request->regiao_id);
        $sistema = Sistema::findOrFail($request->sistema_id);
        
        $valorBase = $servidor->valor * (1 - ($servidor->desconto_percentual / 100));
        $custoAdicional = $valorBase * ($regiao->porcentagem / 100);
        $valorTotal = $valorBase + $custoAdicional;
        
        $contrato = Contrato::create([
            'user_id' => $user->id,
            'servidor_id' => $servidor->id,
            'servidor_api_id' => $opcaoDisco->id,
            'regiao_id' => $regiao->id,
            'sistema_id' => $sistema->id,
            'vCPU' => $servidor->vCPU . ' Core',
            'ram' => $servidor->ram,
            'disk_info' => $opcaoDisco->disk_size,
            'snapshots' => $servidor->snapshots,
            'traffic' => $servidor->traffic,
            'regiao_nome' => $regiao->regiao,
            'sistema_nome' => $sistema->description,
            'valor_total_mensal' => $valorTotal,
            'status' => 'pendente',
            'metodo_pagamento' => $request->metodo_pagamento,
            'gateway_card_id' => $request->metodo_pagamento === 'credit_card' ? $request->card_id : null,
        ]);

        ConfiguracaoPendente::where('user_id', $user->id)->delete();

        if ($request->metodo_pagamento === 'pix') {
            return redirect()->route('pagamento.exibir', ['contrato' => $contrato->id]);
        }

        if ($request->metodo_pagamento === 'credit_card') {
            try {
                MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
                $payment_client = new PaymentClient();
                $idempotencyKey = Uuid::uuid4()->toString();
                $request_options = new RequestOptions();
                $request_options->setCustomHeaders(["X-Idempotency-Key: " . $idempotencyKey]);
                
                $documentoLimpo = preg_replace('/\D/', '', $cliente->identification_number);
                $tipoDocumento = strlen($documentoLimpo) == 11 ? 'CPF' : 'CNPJ';

                $payment_data = [
                    "transaction_amount" => (float) round($valorTotal, 2),
                    "token" => $request->payment_token,
                    "description" => "Assinatura VPS " . $servidor->vCPU,
                    "installments" => 1,
                    "payment_method_id" => $request->payment_method_id,
                    "payer" => [
                        "type" => "customer",
                        "id" => $cliente->customer_id_gateway,
                        "email" => $user->email,
                        "identification" => ["type" => $tipoDocumento, "number" => $documentoLimpo]
                    ]
                ];

                $payment = $payment_client->create($payment_data, $request_options);
                
                Pagamento::create([
                    'user_id' => $user->id,
                    'contrato_id' => $contrato->id,
                    'payment_id_gateway' => $payment->id,
                    'tipo_pagamento' => Pagamento::TIPO_CARTAO,
                    'status' => $payment->status,
                    'status_detalhe' => $payment->status_detail,
                    'valor' => $payment->transaction_amount,
                    'metodo_pagamento' => $payment->payment_method_id,
                    'card_last_four' => $payment->card?->last_four_digits,
                    'parcelas' => $payment->installments,
                    'data_pagamento' => ($payment->status === 'approved') ? Carbon::parse($payment->date_approved) : null,
                ]);

                if ($payment->status === 'approved') {
                    $contrato->update(['status' => 'configurando']);
                    Http::post(route('api.provision.instance', ['contrato' => $contrato->id]));
                    $proximaData = Carbon::now()->addDays(30)->toDateString();
                    $contrato->update(['status' => 'ativo', 'data_proximo_vencimento' => $proximaData]);
                    return redirect()->route('painel.dashboard')->with('success', 'Pagamento aprovado!');
                } else {
                    $contrato->update(['status' => 'falha_autenticacao']);
                    return redirect()->route('painel.dashboard')->with('error', 'Pagamento recusado.');
                }
            } catch (\Exception $e) {
                Log::error($e->getMessage());
                return redirect()->route('painel.dashboard')->with('error', 'Erro ao processar pagamento.');
            }
        }
        return redirect()->route('painel.dashboard');
    }
}