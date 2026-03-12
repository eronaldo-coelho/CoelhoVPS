<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Contrato;
use App\Models\InstanciaVps;
use App\Models\Pagamento;
use App\Models\Regiao;
use App\Models\Servidor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SuporteController extends Controller
{
    public function index()
    {
        return view('painel.suporte.index');
    }

    public function chat(Request $request)
    {
        $request->validate([
            'history' => 'required|array',
        ]);

        $apiKey = config('services.gemini.api_key');
        if (!$apiKey) {
            return response()->json(['error' => 'API Key do Gemini não configurada.'], 500);
        }

        try {
            $context = $this->buildContextForAI();
            $history = $request->input('history', []);

            $formattedContents = array_map(function ($message) {
                return [
                    'role' => $message['role'],
                    'parts' => [['text' => $message['text']]]
                ];
            }, $history);

            $payload = [
                'system_instruction' => [
                    'parts' => [['text' => $context]]
                ],
                'contents' => $formattedContents
            ];

            $response = Http::withHeaders([
                'x-goog-api-key' => $apiKey,
                'Content-Type' => 'application/json',
            ])->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent', $payload);

            if ($response->failed()) {
                Log::error('Erro na API Gemini: ', $response->json());
                return response()->json(['error' => 'Ocorreu um erro ao contatar o assistente.'], 502);
            }
            
            $content = $response->json('candidates.0.content.parts.0.text', 'Não consegui processar sua solicitação no momento.');
            
            return response()->json(['reply' => $content]);

        } catch (\Exception $e) {
            Log::error('Erro no SuporteController@chat: ' . $e->getMessage());
            return response()->json(['error' => 'Ocorreu um erro interno no servidor.'], 500);
        }
    }

    private function buildContextForAI()
    {
        $user = Auth::user();
        $cliente = Cliente::where('user_id', $user->id)->first();
        $contratos = Contrato::where('user_id', $user->id)->with(['servidorApi', 'regiao'])->get();
        $instancias = InstanciaVps::where('user_id', $user->id)->get();
        $pagamentos = Pagamento::where('user_id', $user->id)->latest()->take(5)->get();
        $planosDeServidores = Servidor::with('servidorApi')->get();
        $regioesDisponiveis = Regiao::all();

        $userData = $user->toArray();
        unset($userData['password'], $userData['remember_token'], $userData['email_verified_at']);

        $contextoGeral = "
Você é um assistente de suporte especialista da '<COELHO/>VPS', uma empresa de hospedagem de servidores VPS de alta performance. Seu nome é CoelhoBot. Responda sempre em português do Brasil, de forma amigável, prestativa e técnica. Não invente planos ou dados que não foram fornecidos a você. Você já deu as boas-vindas, então não precisa se apresentar novamente.
Voce ja disse 'Olá' não diga novamente.
INFORMAÇÕES GERAIS SOBRE A PLATAFORMA COELHO VPS:
- Nossos servidores usam processadores AMD e armazenamento NVMe Gen 4 para desempenho superior.
- O provisionamento (instalação) do servidor é rápido, geralmente em minutos.
- Oferecemos 32 TB de tráfego de saída e entrada ilimitada, com velocidade de até 1 Gbit/s.
- Todos os planos incluem Snapshots (cópias de segurança manuais) e proteção contra ataques DDoS.
- O painel de controle é intuitivo e permite o gerenciamento completo dos serviços.
- Descontos: Os descontos percentuais são aplicados TODOS OS MESES sobre o valor base do plano, não apenas no primeiro mês. Por exemplo, um plano de R$39,90 com 10% de desconto custará R$35,91 por mês.
- Regiões: A escolha da região pode adicionar um custo extra baseado em uma porcentagem sobre o valor do plano. A região 'União Europeia (EU)' tem 0% de custo adicional, sendo a opção base.
- Armazenamento: Para cada plano de servidor, o cliente pode escolher entre duas opções de armazenamento (ex: 75 GB NVMe ou 150 GB SSD) sem custo adicional. A escolha é feita no momento da compra.
- Status do Contrato: 'ativo' (serviço funcionando), 'pendente' (aguardando pagamento da primeira fatura), 'configurando' (servidor sendo instalado), 'suspenso' (pagamento atrasado, serviço parado), 'cancelado' (serviço encerrado).

Abaixo estão todos os planos de servidores e regiões que a empresa oferece. Use estas listas para responder a perguntas sobre preços, especificações e disponibilidade.

PLANOS DE SERVIDORES DISPONÍVEIS:
" . json_encode($planosDeServidores, JSON_PRETTY_PRINT) . "

REGIÕES DISPONÍVEIS (a 'porcentagem' é o custo adicional sobre o valor do plano):
" . json_encode($regioesDisponiveis, JSON_PRETTY_PRINT) . "
";

        $contextoUsuario = "
Abaixo estão os dados do cliente com quem você está conversando. Use essas informações para dar respostas personalizadas e precisas sobre os serviços, faturas e status dele.

DADOS DO CLIENTE ATUAL:
- Usuário: " . json_encode($userData, JSON_PRETTY_PRINT) . "
- Dados de Cobrança: " . json_encode($cliente, JSON_PRETTY_PRINT) . "
- Contratos Ativos/Suspensos: " . json_encode($contratos, JSON_PRETTY_PRINT) . "
- Instâncias VPS (Servidores Instalados): " . json_encode($instancias, JSON_PRETTY_PRINT) . "
- Últimos Pagamentos/Faturas: " . json_encode($pagamentos, JSON_PRETTY_PRINT) . "
";

        return $contextoGeral . $contextoUsuario;
    }
}