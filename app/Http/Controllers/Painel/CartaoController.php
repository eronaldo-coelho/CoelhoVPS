<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Customer\CustomerCardClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;

class CartaoController extends Controller
{
    public function index()
    {
        $usuarioId = Auth::id();
        $cliente = Cliente::where('user_id', $usuarioId)->first();
        $cards = collect();

        if ($cliente && $cliente->customer_id_gateway) {
            try {
                MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
                $card_client = new CustomerCardClient();
                $customerCardsResult = $card_client->list($cliente->customer_id_gateway);
                $cards = collect($customerCardsResult->data ?? []);
            } catch (MPApiException $e) {
                Log::error('Erro ao buscar cartões no MP para o usuário ' . $usuarioId . ': ' . $e->getMessage());
                return back()->with('error', 'Não foi possível carregar seus cartões. Tente novamente mais tarde.');
            } catch (\Exception $e) {
                Log::error('Erro geral ao buscar cartões para o usuário ' . $usuarioId . ': ' . $e->getMessage());
            }
        }

        return view('painel.cartoes.index', compact('cards', 'cliente'));
    }

    public function destroy($cardId)
    {
        $usuarioId = Auth::id();
        $cliente = Cliente::where('user_id', $usuarioId)->first();

        if (!$cliente || !$cliente->customer_id_gateway) {
            return redirect()->route('painel.cartoes.index')->with('error', 'Cliente não encontrado ou não possui cadastro no gateway de pagamento.');
        }

        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
            $card_client = new CustomerCardClient();
            $card_client->delete($cliente->customer_id_gateway, $cardId);

            return redirect()->route('painel.cartoes.index')->with('success', 'Cartão removido com sucesso!');

        } catch (MPApiException $e) {
            Log::error('Erro ao remover cartão no MP: ' . json_encode($e->getApiResponse()->getContent()));
            return redirect()->route('painel.cartoes.index')->with('error', 'Não foi possível remover o cartão. Tente novamente.');
        } catch (\Exception $e) {
            Log::error('Erro geral ao remover cartão: ' . $e->getMessage());
            return redirect()->route('painel.cartoes.index')->with('error', 'Ocorreu um erro inesperado.');
        }
    }
}