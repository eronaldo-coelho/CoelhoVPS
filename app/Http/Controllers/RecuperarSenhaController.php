<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ValidacaoTrocaSenha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Carbon\Carbon;

class RecuperarSenhaController extends Controller
{
    public function mostrarFormularioEsqueceu()
    {
        return view('autenticacao.esqueceu-senha');
    }

    public function enviarLinkRecuperacao(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if ($user) {
            $token = Str::random(32);
            
            // Grava usando o horário padrão do sistema (UTC) para evitar erros de cálculo
            ValidacaoTrocaSenha::create([
                'user_id' => $user->id,
                'email' => $user->email,
                'token' => $token,
                'usado' => false,
                'data_horario' => Carbon::now() // Usa o padrão do servidor
            ]);

            $link = route('senha.redefinir.mostrar', ['token' => $token]);

            $this->enviarEmailMailjet($user, $link);
        }

        return back()->with('success', 'Se o e-mail informado estiver em nossa base, você receberá um link para redefinir sua senha em instantes.');
    }

    private function enviarEmailMailjet($user, $link)
    {
        $apiKey = config('services.mailjet.key');
        $apiSecret = config('services.mailjet.secret');

        Http::withBasicAuth($apiKey, $apiSecret)->post('https://api.mailjet.com/v3.1/send', [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "seguranca@coelhovps.com.br",
                        'Name' => "Segurança - CoelhoVPS"
                    ],
                    'To' => [
                        [
                            'Email' => $user->email,
                            'Name' => $user->name
                        ]
                    ],
                    'Subject' => "Recuperação de Senha — CoelhoVPS",
                    'HTMLPart' => view('emails.recuperar-senha', ['user' => $user, 'link' => $link])->render()
                ]
            ]
        ]);
    }

    public function mostrarFormularioRedefinir($token)
    {
        // Busca o token
        $validacao = ValidacaoTrocaSenha::where('token', $token)
            ->where('usado', false)
            ->first();

        if (!$validacao) {
            return redirect()->route('entrar.mostrar')->withErrors(['email' => 'Link inválido ou já utilizado.']);
        }

        // Verifica a expiração (agora de forma correta comparando UTC com UTC)
        $criadoEm = Carbon::parse($validacao->data_horario);
        if ($criadoEm->diffInMinutes(Carbon::now()) > 10) {
            return redirect()->route('entrar.mostrar')->withErrors(['email' => 'Este link expirou (limite de 10 minutos). Solicite um novo.']);
        }

        return view('autenticacao.redefinir-senha', ['token' => $token]);
    }

    public function processarRedefinir(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'password' => 'required|confirmed|min:8',
        ], [
            'password.confirmed' => 'As senhas não coincidem.',
            'password.min' => 'A senha deve ter pelo menos 8 caracteres.'
        ]);

        $validacao = ValidacaoTrocaSenha::where('token', $request->token)
            ->where('usado', false)
            ->first();

        if (!$validacao) {
            return redirect()->route('entrar.mostrar')->withErrors(['email' => 'Solicitação inválida.']);
        }

        // Verifica expiração novamente no processamento por segurança
        if (Carbon::parse($validacao->data_horario)->diffInMinutes(Carbon::now()) > 10) {
            return redirect()->route('entrar.mostrar')->withErrors(['email' => 'O prazo de 10 minutos expirou.']);
        }

        $user = User::find($validacao->user_id);
        $user->password = Hash::make($request->password);
        $user->save();

        // Marca como usado
        $validacao->usado = true;
        $validacao->save();

        return redirect()->route('entrar.mostrar')->with('success', 'Senha alterada com sucesso! Você já pode logar.');
    }
}