<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ConfiguracaoPendente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Customer\CustomerClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Net\MPSearchRequest;

class ClienteController extends Controller
{
    public function create()
    {
        return view('cliente.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone_area_code' => 'required|string|max:4',
            'phone_number' => 'required|string|max:25',
            'identification_number' => 'required|string|max:25',
            'address_zip_code' => 'required|string|max:10',
            'address_street_name' => 'required|string|max:255',
            'address_street_number' => 'required|string|max:10',
        ]);

        $sanitizedData = [];
        foreach ($validatedData as $key => $value) {
            if (in_array($key, ['phone_area_code', 'phone_number', 'identification_number', 'address_zip_code'])) {
                $sanitizedData[$key] = preg_replace('/[^0-9]/', '', $value);
            }
        }

        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
            $client = new CustomerClient();
            $userEmail = Auth::user()->email;

            $filters = ["email" => $userEmail];
            $requestSearchParams = new MPSearchRequest(1, 0, $filters);
            $searchResult = $client->search($requestSearchParams);

            $customer = null;

            if (!empty($searchResult->results)) {
                $customer = $searchResult->results[0];
            } else {
                $customer = $client->create([
                    "email" => $userEmail,
                    "first_name" => $validatedData['first_name'],
                    "last_name" => $validatedData['last_name'],
                    "phone" => [
                        "area_code" => $sanitizedData['phone_area_code'],
                        "number" => $sanitizedData['phone_number']
                    ],
                    "identification" => [
                        "type" => "CPF",
                        "number" => $sanitizedData['identification_number']
                    ],
                ]);
            }

            Cliente::updateOrCreate(
                ['user_id' => Auth::id()],
                array_merge($validatedData, [
                    'customer_id_gateway' => $customer->id,
                    'identification_type' => 'CPF',
                    'identification_number' => $sanitizedData['identification_number'],
                    'phone_area_code' => $sanitizedData['phone_area_code'],
                    'phone_number' => $sanitizedData['phone_number'],
                    'address_zip_code' => $sanitizedData['address_zip_code']
                ])
            );

            Auth::user()->update([
                'cpf' => $sanitizedData['identification_number'],
                'telefone' => $sanitizedData['phone_area_code'] . $sanitizedData['phone_number']
            ]);

            $config = ConfiguracaoPendente::where('user_id', Auth::id())->first();

            if ($config) {
                return redirect()->route('checkout.iniciar');
            }

            return redirect()->route('painel.dashboard')->with('success', 'Dados salvos!');

        } catch (MPApiException $e) {
            Log::error($e->getApiResponse()->getContent());
            return back()->withErrors('Erro Gateway.')->withInput();
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return back()->withErrors('Erro inesperado.')->withInput();
        }
    }
}