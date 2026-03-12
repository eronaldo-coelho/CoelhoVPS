<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use MercadoPago\Client\Customer\CustomerCardClient;
use MercadoPago\MercadoPagoConfig;
use App\Models\Cliente;
use Exception;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class CartaoController extends Controller
{
    public function create()
    {
        if (Cliente::where('user_id', Auth::id())->count() == 0) {
            return redirect()->route('cliente.create')->with('error', 'Você precisa criar um cliente antes de adicionar um cartão.');
        }

        Session::put('url.intended', URL::previous());
        return view('cartao.adicionar');
    }

    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        try {
            $user = Auth::user();
            $cliente = Cliente::where('user_id', $user->id)->first();
            
            if (!$cliente || !$cliente->customer_id_gateway) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Cliente não encontrado ou não possui ID de gateway.'
                ], 404);
            }

            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));

            $card_client = new CustomerCardClient();
            $card = $card_client->create($cliente->customer_id_gateway, [
                "token" => $request->input('token')
            ]);
            
            $defaultUrl = route('painel.dashboard');
            $redirectUrl = Session::pull('url.intended', $defaultUrl);
            
            if ($redirectUrl === route('cartao.create')) {
                 $redirectUrl = $defaultUrl;
            }

            return response()->json([
                'status' => 'success',
                'message' => 'Cartão adicionado com sucesso!',
                'card_id' => $card->id,
                'redirect_url' => $redirectUrl
            ]);

        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Não foi possível salvar o cartão. Detalhes: ' . $e->getMessage()
            ], 500);
        }
    }
}