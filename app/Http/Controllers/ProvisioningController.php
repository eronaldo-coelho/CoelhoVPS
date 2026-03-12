<?php

namespace App\Http\Controllers;

use App\Models\Contrato;
use App\Models\InstanciaVps;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

class ProvisioningController extends Controller
{
    private function getAccessToken(): ?string
    {
        Log::debug('[CONTABO_AUTH] Iniciando requisição de Access Token.');
        try {
            $response = Http::asForm()->post('https://auth.contabo.com/auth/realms/contabo/protocol/openid-connect/token', [
                'client_id' => config('services.contabo.client_id'),
                'client_secret' => config('services.contabo.client_secret'),
                'username' => config('services.contabo.api_user'),
                'password' => config('services.contabo.api_password'),
                'grant_type' => 'password',
            ]);

            if ($response->failed()) {
                Log::error('[CONTABO_AUTH] Falha ao obter Access Token da Contabo.', [
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body()
                ]);
                return null;
            }

            $token = $response->json('access_token');
            Log::info('[CONTABO_AUTH] Access Token obtido com sucesso.');
            return $token;

        } catch (Throwable $e) {
            Log::critical('[CONTABO_AUTH] Exceção crítica durante a obtenção do Access Token.', [
                'error_message' => $e->getMessage()
            ]);
            return null;
        }
    }

    public function provisionInstance(Contrato $contrato)
    {
        $logContext = ['contrato_id' => $contrato->id, 'user_id' => $contrato->user_id];
        Log::info('[PROVISIONING_START] Processo de provisionamento iniciado.', $logContext);

        if ($contrato->status !== 'configurando') {
            Log::warning('[PROVISIONING_VALIDATION_FAIL] Status do contrato inválido.', array_merge($logContext, ['status_atual' => $contrato->status]));
            return response()->json(['error' => 'Contrato não está com status configurando'], 400);
        }

        if (InstanciaVps::where('contrato_id', $contrato->id)->exists()) {
            Log::warning('[PROVISIONING_VALIDATION_FAIL] Instância para este contrato já existe.', $logContext);
            return response()->json(['error' => 'Instância já provisionada para este contrato'], 409);
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            $contrato->update(['status' => 'falha_autenticacao']);
            Log::error('[PROVISIONING_AUTH_FAIL] Falha na autenticação com a Contabo. Processo abortado.', $logContext);
            return response()->json(['error' => 'Falha na autenticação com a Contabo'], 500);
        }

        $contrato->load(['regiao', 'servidorApi', 'sistema']);
        $imagemId = $contrato->sistema->image_id;
        $productId = $contrato->servidorApi->product_id;
        $region = $contrato->regiao->regiao_id;
        $rootPassword = Str::random(12);

        Log::info('[PROVISIONING_DATA] Dados necessários para provisionamento carregados.', array_merge($logContext, [
            'imagem_id' => $imagemId,
            'product_id' => $productId,
            'region' => $region
        ]));

        // Script Cloud-Init (userData) Formatado e com Branding incluso
        $userData = <<<YAML
#cloud-config
hostname: CoelhoVPS
manage_etc_hosts: true
package_update: true
packages:
  - openssh-server
ssh_pwauth: true
disable_root: false
users:
  - name: coelhovps
    groups: sudo
    shell: /bin/bash
    sudo: ALL=(ALL) NOPASSWD:ALL
    lock_passwd: false
chpasswd:
  list: |
    coelhovps:Coelho12
  expire: False
write_files:
  - path: /etc/motd
    content: |
      ####################################################
      #                                                  #
      #               CoelhoVPS - Bem-vindo              #
      #         Servidor gerenciado pela CoelhoVPS       #
      #                                                  #
      ####################################################
  - path: /etc/issue
    content: |
      CoelhoVPS - Acesso autorizado somente para administradores.
  - path: /etc/issue.net
    content: |
      CoelhoVPS - Acesso autorizado somente para administradores.
  - path: /etc/ssh/coelhovps_banner
    content: |
      CoelhoVPS - Conexão autorizada.
  - path: /etc/profile.d/99-coelhovps-prompt.sh
    permissions: '0755'
    content: |
      export PS1="\[\e[1;32m\]CoelhoVPS:\w\$ \[\e[0m\]"
runcmd:
  - systemctl enable ssh || systemctl enable sshd
  - sed -i 's/^#*PasswordAuthentication.*/PasswordAuthentication yes/' /etc/ssh/sshd_config
  - sed -i 's/^#*PermitRootLogin.*/PermitRootLogin yes/' /etc/ssh/sshd_config
  - sed -i '/^Banner/d' /etc/ssh/sshd_config
  - echo "Banner /etc/ssh/coelhovps_banner" >> /etc/ssh/sshd_config
  - chmod -x /etc/update-motd.d/* >/dev/null 2>&1 || true
  - rm -f /etc/motd.d/* >/dev/null 2>&1 || true
  - systemctl restart ssh || systemctl restart sshd
YAML;
        
        $payload = [
            'imageId' => $imagemId,
            'productId' => $productId,
            'region' => $region,
            'period' => 1,
            'displayName' => "VPS-Contrato-{$contrato->id}",
            'defaultUser' => 'root',
            'userData' => $userData
        ];
        Log::debug('[PROVISIONING_PAYLOAD] Payload para a API da Contabo montado.', array_merge($logContext, ['payload' => $payload]));

        try {
            Log::info('[PROVISIONING_API_CALL] Enviando requisição para criar instância na Contabo.', $logContext);
            $response = Http::withToken($accessToken)
                ->withHeaders(['x-request-id' => (string) Str::uuid()])
                ->post('https://api.contabo.com/v1/compute/instances', $payload);

            Log::debug('[PROVISIONING_API_RESPONSE] Resposta recebida da API da Contabo.', array_merge($logContext, [
                'status' => $response->status(),
                'body' => $response->json() ?? $response->body()
            ]));

            if ($response->failed()) {
                $contrato->update(['status' => 'falha_provisionamento']);
                Log::error('[PROVISIONING_API_FAIL] Falha ao criar instância na Contabo (HTTP Error).', array_merge($logContext, [
                    'status' => $response->status(),
                    'response' => $response->json() ?? $response->body()
                ]));
                return response()->json(['error' => 'API da Contabo retornou um erro.'], 502);
            }

            $instanceData = $response->json('data.0');
            
            // VERIFICAÇÃO ADICIONAL DO STATUS DA RESPOSTA
            if ($instanceData['status'] === 'pending_payment') {
                $contrato->update(['status' => 'falha_provisionamento']);
                Log::error('[PROVISIONING_API_FAIL] Contabo retornou status "pending_payment". Verifique o método de pagamento da conta.', array_merge($logContext, [
                    'instance_data' => $instanceData
                ]));
                return response()->json(['error' => 'A criação da instância requer pagamento na Contabo.'], 402);
            }

            $newInstanceData = [
                'user_id' => $contrato->user_id,
                'contrato_id' => $contrato->id,
                'instance_id_contabo' => $instanceData['instanceId'],
                'display_name' => $instanceData['displayName'] ?? "VPS-Contrato-{$contrato->id}",
                'status' => $instanceData['status'],
                'root_password' => $rootPassword,
                'full_response_data' => $instanceData,
            ];

            Log::debug('[PROVISIONING_DB_CREATE] Dados prontos para salvar no banco de dados.', array_merge($logContext, ['data' => $newInstanceData]));
            $newInstance = InstanciaVps::create($newInstanceData);

            $contrato->update(['status' => 'ativo']);
            Log::info('[PROVISIONING_SUCCESS] Instância criada e contrato ativado com sucesso.', array_merge($logContext, [
                'instancia_vps_id' => $newInstance->id,
                'instance_id_contabo' => $instanceData['instanceId']
            ]));
            
            $finalResponse = [
                'message' => 'Instância criada e contrato ativado com sucesso!',
                'instance_id' => $newInstance->id,
            ];

            Log::debug('[PROVISIONING_END] Resposta final do endpoint.', array_merge($logContext, ['response' => $finalResponse]));
            return response()->json($finalResponse, 201);

        } catch (Throwable $e) {
            $contrato->update(['status' => 'falha_provisionamento']);
            Log::critical('[PROVISIONING_EXCEPTION] Exceção crítica durante o provisionamento.', array_merge($logContext, [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]));
            return response()->json(['error' => 'Ocorreu um erro interno inesperado.'], 500);
        }
    }
}