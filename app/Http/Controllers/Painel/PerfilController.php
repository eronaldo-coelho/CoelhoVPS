<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use MercadoPago\Client\Customer\CustomerClient;
use MercadoPago\Exceptions\MPApiException;
use MercadoPago\MercadoPagoConfig;
use Illuminate\Validation\Rule;

class PerfilController extends Controller
{
    public function edit()
    {
        $cliente = Cliente::where('user_id', Auth::id())->first();
        
        if (!$cliente) {
            session()->flash('redirect_to_profile', true);
            return redirect("/cliente/completar-cadastro");
        }

        return view('painel.perfil.edit', compact('cliente'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $cliente = $user->cliente()->firstOrFail();

        $validatedData = $request->validate([
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password' => 'nullable|string|min:8|confirmed',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'phone_area_code' => 'required|string|max:4',
            'phone_number' => 'required|string|max:25',
            'identification_number' => 'required|string|max:25',
            'address_zip_code' => 'required|string|max:10',
            'address_street_name' => 'required|string|max:255',
            'address_street_number' => 'required|string|max:10',
        ]);

        $user->name = $validatedData['first_name'] . ' ' . $validatedData['last_name'];
        $user->email = $validatedData['email'];
        if ($request->filled('password')) {
            $user->password = Hash::make($validatedData['password']);
        }
        $user->save();

        $cliente->update($validatedData);

        try {
            MercadoPagoConfig::setAccessToken(config('services.mercadopago.access_token'));
            $mpClient = new CustomerClient();

            $phone_number_sanitized = preg_replace('/[^0-9]/', '', $validatedData['phone_number']);
            $identification_number_sanitized = preg_replace('/[^0-9]/', '', $validatedData['identification_number']);

            $mpClient->update($cliente->customer_id_gateway, [
                "emai" => $validatedData['email'],
                "first_name" => $validatedData['first_name'],
                "last_name" => $validatedData['last_name'],
                "phone" => [
                    "area_code" => $validatedData['phone_area_code'],
                    "number" => $phone_number_sanitized
                ],
                "identification" => [
                    "type" => "CPF",
                    "number" => $identification_number_sanitized
                ],
            ]);
        } catch (MPApiException $e) {
            Log::error("Erro API MP ao atualizar cliente {$cliente->id}: ", $e->getApiResponse()->getContent());
            return back()->with('error', 'Seus dados foram salvos, mas falhou ao atualizar no gateway de pagamento. Verifique os dados ou contate o suporte.')->withInput();
        } catch (\Exception $e) {
            Log::error("Erro geral ao atualizar cliente {$cliente->id}: " . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro inesperado. Tente novamente mais tarde.')->withInput();
        }

        return redirect()->route('painel.perfil.edit')->with('success', 'Seu perfil foi atualizado com sucesso!');
    }
}