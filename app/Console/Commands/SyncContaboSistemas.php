<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Sistema;

class SyncContaboSistemas extends Command
{
    protected $signature = 'contabo:sync-sistemas';
    protected $description = 'Sincroniza todas as imagens (sistemas) da API Contabo e salva na tabela sistemas.';

    public function handle()
    {
        $this->info('🔄 Iniciando — verificando credenciais nas configs...');

        $clientId = config('services.contabo.client_id');
        $clientSecret = config('services.contabo.client_secret');
        $apiUser = config('services.contabo.api_user');
        $apiPassword = config('services.contabo.api_password');

        // Verificações iniciais simples (sem imprimir segredos)
        if (!$clientId || !$clientSecret || !$apiUser || !$apiPassword) {
            $this->error('❌ Falha: alguma credencial do contabo está faltando em config/services.php ou .env.');
            $this->line('Verifique: CONTABO_CLIENT_ID, CONTABO_CLIENT_SECRET, CONTABO_API_USER, CONTABO_API_PASSWORD');
            return Command::FAILURE;
        }

        $this->info('✅ Credenciais presentes. Tentando obter token via Http::asForm()...');

        // 1) Tentar com Http::asForm()
        $authResponse = Http::asForm()->post(
            'https://auth.contabo.com/auth/realms/contabo/protocol/openid-connect/token',
            [
                'client_id' => $clientId,
                'client_secret' => $clientSecret,
                'username' => $apiUser,
                'password' => $apiPassword,
                'grant_type' => 'password',
            ]
        );

        if ($authResponse->successful()) {
            $token = $authResponse->json('access_token');
            if ($token) {
                $this->info('✅ Token obtido com sucesso via Http::asForm().');
                return $this->syncImagesWithToken($token);
            }
        }

        // Se chegou aqui, houve problema com Http::asForm()
        $this->warn('⚠️ Http::asForm() falhou — resposta bruta:');
        $this->line($authResponse->body());

        // 2) Tentar com curl (fallback) — replica o exemplo oficial (data-urlencode para username/password)
        $this->info('🔁 Tentando obter token via curl (fallback) — isto replica o exemplo oficial contabo...');

        // Construir comando curl com --data-urlencode para username e password
        $curlCmd = sprintf(
            "curl -s -X POST %s -d 'client_id=%s' -d 'client_secret=%s' --data-urlencode 'username=%s' --data-urlencode 'password=%s' -d 'grant_type=password'",
            escapeshellarg('https://auth.contabo.com/auth/realms/contabo/protocol/openid-connect/token'),
            escapeshellarg($clientId),
            escapeshellarg($clientSecret),
            escapeshellarg($apiUser),
            escapeshellarg($apiPassword)
        );

        // Executa
        $this->line("🔧 Executando curl (mas sem imprimir credenciais raw)...");
        exec($curlCmd . ' 2>&1', $outputLines, $exitCode);
        $curlOutput = implode("\n", $outputLines);

        if ($exitCode !== 0) {
            $this->error("❌ curl retornou exit code {$exitCode}. Saída:\n" . $curlOutput);
            $this->line('Sugestões: verifique se o servidor tem curl instalado e acesso à internet, e se as credenciais são válidas no painel Contabo.');
            return Command::FAILURE;
        }

        // tentar decodificar JSON retornado pelo curl
        $json = json_decode($curlOutput, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error('❌ Resposta do curl não é JSON válido. Conteúdo retornado:');
            $this->line($curlOutput);
            return Command::FAILURE;
        }

        if (!empty($json['access_token'])) {
            $this->info('✅ Token obtido com sucesso via curl fallback.');
            return $this->syncImagesWithToken($json['access_token']);
        }

        // Se nenhum token foi obtido, mostra o erro detalhado
        $this->error('❌ Ainda sem token. Resposta do servidor:');
        $this->line(json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        // Possíveis causas comuns (mensagem ao usuário)
        $this->warn("Possíveis causas:\n - client_id/client_secret incorretos\n - api_user/api_password incorretos (verifique se você criou a senha de API no painel)\n - conta ou credenciais com bloqueio\n - problemas de encoding (caracteres especiais na senha)\n\nRecomendações rápidas:\n - Verifique e recadastre as credenciais no Painel do Cliente Contabo\n - Tente o mesmo comando curl direto no shell para ver a saída (já replicada aqui)\n - Rode: php artisan config:clear && php artisan cache:clear && php artisan config:cache");

        return Command::FAILURE;
    }

    /**
     * Faz a sincronização de imagens usando o token obtido.
     */
    protected function syncImagesWithToken(string $token)
    {
        $this->info('🔄 Buscando imagens padrão na API Contabo...');

        $page = 1;
        $perPage = 50;
        $total = 0;

        do {
            $response = Http::withToken($token)
                ->withHeaders([
                    'x-request-id' => (string) Str::uuid(),
                    'Content-Type' => 'application/json',
                ])
                ->get('https://api.contabo.com/v1/compute/images', [
                    'standardImage' => true,
                    'page' => $page,
                    'size' => $perPage,
                ]);

            if (!$response->successful()) {
                $this->error("❌ Erro ao buscar página {$page}: " . $response->body());
                return Command::FAILURE;
            }

            $data = $response->json('data') ?? [];

            if (empty($data)) {
                break;
            }

            foreach ($data as $img) {
                Sistema::updateOrCreate(
                    ['image_id' => $img['imageId']],
                    [
                        'tenant_id' => $img['tenantId'] ?? null,
                        'customer_id' => $img['customerId'] ?? null,
                        'name' => $img['name'] ?? null,
                        'description' => $img['description'] ?? null,
                        'url' => $img['url'] ?? null,
                        'size_mb' => $img['sizeMb'] ?? null,
                        'uploaded_size_mb' => $img['uploadedSizeMb'] ?? null,
                        'os_type' => $img['osType'] ?? null,
                        'version' => $img['version'] ?? null,
                        'format' => $img['format'] ?? null,
                        'status' => $img['status'] ?? null,
                        'standard_image' => $img['standardImage'] ?? false,
                        'creation_date' => $img['creationDate'] ?? null,
                        'last_modified_date' => $img['lastModifiedDate'] ?? null,
                        'tags' => $img['tags'] ?? [],
                    ]
                );

                $total++;
            }

            $page++;
        } while (!empty($data));

        $this->info("✅ Sincronização finalizada. Total de imagens salvas/atualizadas: {$total}");
        return Command::SUCCESS;
    }
}
