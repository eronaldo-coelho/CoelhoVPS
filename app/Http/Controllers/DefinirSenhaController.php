<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracaoPendente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class DefinirSenhaController extends Controller
{
    public function mostrarFormulario()
    {
        if (auth()->user()->password) {
            $config = ConfiguracaoPendente::where('user_id', auth()->id())->first();
            if ($config) {
                return redirect()->route('checkout.iniciar');
            }
            return redirect()->route('painel.dashboard');
        }
        return view('autenticacao.definir-senha');
    }

    public function processarFormulario(Request $request)
    {
        $request->validate([
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        auth()->user()->update([
            'password' => Hash::make($request->password),
        ]);

        $config = ConfiguracaoPendente::where('user_id', auth()->id())->first();

        if ($config) {
            return redirect()->route('checkout.iniciar');
        }

        return redirect()->route('painel.dashboard')->with('status', 'Senha definida!');
    }
}