<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class AutenticacaoController extends Controller
{
    public function mostrarFormularioRegistro()
    {
        return view('autenticacao.registrar');
    }

    public function processarRegistro(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $config = session('configuracao_pendente');

        $usuario = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Auth::login($usuario);

        if ($config) {
            session(['configuracao_pendente' => $config]);
            session()->save();
            return redirect()->route('checkout.iniciar');
        }

        return redirect()->route('painel.dashboard');
    }

    public function mostrarFormularioEntrar()
    {
        return view('autenticacao.entrar');
    }

    public function processarEntrar(Request $request)
    {
        $credenciais = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $config = session('configuracao_pendente');

        if (Auth::attempt($credenciais, $request->boolean('remember'))) {
            $request->session()->regenerate();

            if ($config) {
                session(['configuracao_pendente' => $config]);
                session()->save();
                return redirect()->route('checkout.iniciar');
            }

            return redirect()->intended(route('painel.dashboard'));
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registros.',
        ])->onlyInput('email');
    }

    public function sair(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function redirecionarParaGoogle()
    {
        $url = 'https://accounts.google.com/o/oauth2/v2/auth';
        $redirectUri = 'https://coelhovps.com.br/autenticacao/google/callback';
        $parametros = [
            'client_id' => config('services.google.client_id'),
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => 'openid profile email',
        ];
        $urlCompleta = $url . '?' . http_build_query($parametros);
        return redirect($urlCompleta);
    }

    public function lidarComCallbackDoGoogle(Request $request)
    {
        if ($request->has('error')) {
            return redirect()->route('entrar.mostrar')->withErrors(['email' => 'Não foi possível autenticar com o Google.']);
        }

        $config = session('configuracao_pendente');

        $respostaToken = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'client_id' => config('services.google.client_id'),
            'client_secret' => config('services.google.client_secret'),
            'code' => $request->code,
            'redirect_uri' => 'https://coelhovps.com.br/autenticacao/google/callback',
            'grant_type' => 'authorization_code',
        ]);

        if ($respostaToken->failed()) {
            return redirect()->route('entrar.mostrar')->withErrors(['email' => 'Falha ao obter token de acesso.']);
        }

        $accessToken = $respostaToken->json()['access_token'];
        $respostaUsuario = Http::withHeaders(['Authorization' => 'Bearer ' . $accessToken])->get('https://www.googleapis.com/oauth2/v2/userinfo');
        
        $dadosUsuarioGoogle = $respostaUsuario->json();
        
        $usuario = User::updateOrCreate(
            ['email' => $dadosUsuarioGoogle['email']],
            [
                'name' => $dadosUsuarioGoogle['name'],
                'google_id' => $dadosUsuarioGoogle['id'],
            ]
        );

        Auth::login($usuario);

        if ($config) {
            session(['configuracao_pendente' => $config]);
            session()->save();
            return redirect()->route('checkout.iniciar');
        }

        return redirect()->intended(route('painel.dashboard'));
    }
}