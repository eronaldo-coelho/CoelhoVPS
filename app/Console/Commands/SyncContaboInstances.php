<?php

namespace App\Console\Commands;

use App\Models\Contrato;
use App\Models\InstanciaVps;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;
use Throwable;

class SyncContaboInstances extends Command
{
    /**
     * A assinatura do comando do console.
     */
    protected $signature = 'app:sync-contabo-instances {--loop : Roda o comando em loop a cada 20 segundos}';

    /**
     * A descrição do comando do console.
     */
    protected $description = 'Sincroniza instâncias, reseta senha se necessário e aplica branding automaticamente.';

    private $apiBaseUrl = 'https://api.contabo.com/v1';
    private $authUrl = 'https://auth.contabo.com/auth/realms/contabo/protocol/openid-connect/token';

    /**
     * Executa o comando do console.
     */
    public function handle(): int
    {
        if ($this->option('loop')) {
            $this->info('Comando rodando em modo loop. Pressione Ctrl+C para parar.');
            while (true) {
                try {
                    $this->runSyncLogic();
                } catch (Throwable $e) {
                    $this->error('Ocorreu um erro crítico no loop: ' . $e->getMessage());
                    Log::critical('Erro no SyncContaboInstances (loop): ' . $e->getMessage(), ['exception' => $e]);
                }
                $this->info('Aguardando 20 segundos para a próxima execução...');
                sleep(20);
            }
        } else {
            $this->info('Rodando o comando uma única vez.');
            $this->runSyncLogic();
        }

        return Command::SUCCESS;
    }

/**
     * Contém a lógica principal da sincronização.
     */
    private function runSyncLogic(): void
    {
        $this->line('----------------------------------------------------');
        $this->info(now()->format('Y-m-d H:i:s') . ' - Iniciando sincronização...');

        $instances = $this->getAllInstances();
        if (empty($instances)) {
            $this->warn('Nenhuma instância encontrada na Contabo ou houve um erro na API.');
            return;
        }

        $this->info(count($instances) . ' instâncias encontradas. Processando...');

        foreach ($instances as $instance) {
            $displayName = $instance['displayName'] ?? 'N/A';
            $instanceId = $instance['instanceId'];
            $ipV4 = $instance['ipConfig']['v4']['ip'] ?? null;
            $apiStatus = $instance['status']; // Pega o status real atual da API da Contabo

            if (!preg_match('/-([0-9]+)$/', $displayName, $matches)) {
                $this->warn("ID do Contrato não encontrado em '{$displayName}'. Pulando instância ID {$instanceId}.");
                continue;
            }
            $contratoId = (int)$matches[1];

            $contrato = Contrato::find($contratoId);
            if (!$contrato) {
                $this->warn("Contrato ID {$contratoId} não existe no banco. Pulando instância ID {$instanceId}.");
                continue;
            }
            
            $this->line("Processando Instância ID: {$instanceId} (Contrato: {$contratoId})");
            $localInstance = InstanciaVps::where('instance_id_contabo', $instanceId)->first();
            
            // 1. SE A INSTÂNCIA É NOVA OU AINDA NÃO TEM SENHA DEFINIDA
            if (!$localInstance || empty($localInstance->root_password)) {
                $this->info(" > Instância nova ou sem senha. Resetando senha na Contabo...");
                $newPassword = Str::random(8);
                $passwordResetSuccess = $this->resetInstancePassword($instanceId, $newPassword);

                if (!$passwordResetSuccess) {
                    $this->error(" > Falha ao resetar a senha. A criação/atualização no banco foi cancelada.");
                    continue;
                }
                
                $this->info(" > Senha resetada com sucesso para: {$newPassword}");
                
                // Salva todos os dados da VPS nova no banco
                $localInstance = InstanciaVps::updateOrCreate(
                    ['instance_id_contabo' => $instanceId],
                    [
                        'user_id'            => $contrato->user_id,
                        'contrato_id'        => $contratoId,
                        'display_name'       => $displayName,
                        'ip_v4'              => $ipV4 ?? 'N/A',
                        'status'             => $apiStatus, // Salva o status atual
                        'full_response_data' => $instance,
                        'root_password'      => $newPassword,
                    ]
                );

                // <<-- NOVO BLOCO PARA CHAMAR A ROTA DE BRANDING -->>
                if ($ipV4) {
                    $this->info(" > Aplicando branding no servidor {$ipV4}...");
                    try {
                        $brandingResponse = Http::timeout(400) // Define o timeout em segundos ANTES de chamar o post
                            ->post(url('/configurar/servidor'), [
                                'ip' => $ipV4,
                                'username' => 'root',
                                'password' => $newPassword,
                            ]);

                        if ($brandingResponse->successful()) {
                            $this->info(" > Branding aplicado com sucesso.");
                            Contrato::where('id', $contratoId)->update(['status' => 'ativo']);
                        } else {
                            $this->warn(" > Falha ao aplicar branding. Status: " . $brandingResponse->status());
                            Log::warning("Falha ao aplicar branding para IP {$ipV4}", [
                                'status' => $brandingResponse->status(),
                                'body' => $brandingResponse->body()
                            ]);
                        }
                    } catch (Throwable $e) {
                        $this->error(" > Exceção ao tentar aplicar branding: " . $e->getMessage());
                        Log::error("Exceção ao chamar API de branding para IP {$ipV4}", ['exception' => $e]);
                    }
                } else {
                    $this->warn(" > Não foi possível aplicar branding: IP do servidor não encontrado.");
                }
                // <<-- FIM DO NOVO BLOCO -->>

            } 
            // 2. SE A INSTÂNCIA JÁ EXISTE NO BANCO (Mexe apenas no status se for diferente)
            else {
                if ($localInstance->status !== $apiStatus) {
                    $this->info(" > Status alterado de '{$localInstance->status}' para '{$apiStatus}'. Atualizando no banco...");
                    
                    // Atualiza APENAS o status e o JSON da resposta (pra manter cache limpo), sem mexer em senhas ou IPs
                    $localInstance->status = $apiStatus;
                    $localInstance->full_response_data = $instance;
                    $localInstance->save();
                    
                } else {
                    $this->info(" > Status continua como '{$apiStatus}'. Nenhuma alteração feita.");
                }
            }
        }

        $this->info('Sincronização concluída.');
    }
    
    // O resto do código (getAccessToken, getAllInstances, resetInstancePassword) permanece o mesmo

    private function getAccessToken(): ?string
    {
        return Cache::remember('contabo_access_token', 240, function () {
            $response = Http::asForm()->post($this->authUrl, [
                'client_id'     => config('services.contabo.client_id'),
                'client_secret' => config('services.contabo.client_secret'),
                'username'      => config('services.contabo.api_user'),
                'password'      => config('services.contabo.api_password'),
                'grant_type'    => 'password',
            ]);
            if ($response->successful()) {
                $this->info('Token de acesso obtido/renovado com sucesso.');
                return $response->json('access_token');
            }
            Log::error('Falha ao obter token da Contabo: ' . $response->body());
            $this->error('ERRO FATAL: Não foi possível obter o token de acesso da Contabo.');
            return null;
        });
    }

    private function getAllInstances(): array
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) return [];
        $allInstances = [];
        $page = 1;
        do {
            $response = Http::withToken($accessToken)
                ->withHeaders(['x-request-id' => Uuid::uuid4()->toString()])
                ->get("{$this->apiBaseUrl}/compute/instances", ['page' => $page, 'size' => 100]);
            if (!$response->successful()) {
                Log::error("Erro ao buscar instâncias (Página {$page}): " . $response->body());
                $this->error("Erro ao buscar página {$page} de instâncias.");
                return [];
            }
            $data = $response->json();
            $allInstances = array_merge($allInstances, $data['data']);
            $totalPages = $data['_pagination']['totalPages'];
            $page++;
        } while ($page <= $totalPages);
        return $allInstances;
    }

    private function resetInstancePassword(int $instanceId, string $newPassword): bool
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) return false;
        $userData = "#cloud-config\nuser: root\nssh_pwauth: true\ndisable_root: false\nchpasswd:\n  list:\n    - root:{$newPassword}\n  expire: False\n";
        $response = Http::withToken($accessToken)
            ->withHeaders(['x-request-id' => Uuid::uuid4()->toString()])
            ->post("{$this->apiBaseUrl}/compute/instances/{$instanceId}/actions/resetPassword", ['userData' => $userData]);
        if (!$response->successful()) {
            $errorMessage = $response->body();
            Log::error("Falha ao resetar senha da instância {$instanceId} via userData: " . $errorMessage);
            $this->error(" > Erro da API ao resetar senha: {$errorMessage}");
            return false;
        }
        return true;
    }
}