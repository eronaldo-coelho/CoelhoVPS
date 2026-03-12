<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\InstanciaVps;
use App\Models\Pagamento;
use App\Models\Ticket;
use App\Models\Sistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    private $apiUrl;
    private $authUrl;

    public function __construct()
    {
        $this->apiUrl = 'https://api.contabo.com';
        $this->authUrl = 'https://auth.contabo.com/auth/realms/contabo/protocol/openid-connect/token';
    }

    private function getAccessToken(): ?string
    {
        return Cache::remember('contabo_access_token', 240, function () {
            $response = Http::asForm()->post($this->authUrl, [
                'client_id' => config('services.contabo.client_id'),
                'client_secret' => config('services.contabo.client_secret'),
                'username' => config('services.contabo.api_user'),
                'password' => config('services.contabo.api_password'),
                'grant_type' => 'password',
            ]);

            if ($response->successful()) {
                return $response->json('access_token');
            }

            Log::error('Falha ao obter token da Contabo: ' . $response->body());
            return null;
        });
    }

    public function index()
    {
        $usuarioId = Auth::id();
        $tickets_abertos = Ticket::where('user_id', $usuarioId)->where('resolvido', false)->count();
        $servidoresAtivos = Contrato::where('user_id', $usuarioId)->where('status', 'ativo')->count();
        $faturasPendentes = Pagamento::where('user_id', $usuarioId)->where('status', 'pending')->count();
        $contratos = Contrato::with('instancia')->where('user_id', $usuarioId)->latest()->get();

        // Lógica de Sistemas Operacionais permitidos
        $osPermitidos = [
            'ubuntu' => 'Ubuntu',
            'debian' => 'Debian',
            'almalinux' => 'AlmaLinux',
            'rockylinux' => 'Rocky Linux',
        ];

        $query = Sistema::query();
        foreach (array_keys($osPermitidos) as $os) {
            $query->orWhere('name', 'like', $os . '%');
        }
        $sistemas = $query->orderBy('name')->get();

        $sistemasAgrupados = $sistemas->groupBy(function ($item) use ($osPermitidos) {
            foreach ($osPermitidos as $prefix => $name) {
                if (Str::startsWith(strtolower($item->name), $prefix)) {
                    return $name;
                }
            }
            return 'Outros';
        });

        return view('painel.dashboard', compact('servidoresAtivos', 'faturasPendentes', 'contratos', 'tickets_abertos', 'sistemasAgrupados'));
    }

    public function gerenciarInstancia(Request $request, Contrato $contrato, $action)
    {
        if ($contrato->user_id !== Auth::id()) abort(403);

        if ($contrato->status !== 'ativo' && in_array($action, ['start', 'restart'])) {
            return back()->with('error', 'Seu contrato está suspenso. Realize o pagamento para liberar esta função.');
        }
        
        $instancia = $contrato->instancia;
        if (!$instancia || !in_array($action, ['start', 'stop', 'restart', 'shutdown'])) {
            return back()->with('error', 'Ação inválida ou instância não encontrada.');
        }

        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return back()->with('error', 'Falha de autenticação com o provedor.');
        }

        try {
            $url = $this->apiUrl . '/v1/compute/instances/' . $instancia->instance_id_contabo . '/actions/' . $action;
            
            // CORREÇÃO: Forçar Content-Type e enviar corpo vazio (array) para o Laravel montar o JSON
            $response = Http::withToken($accessToken)
                ->withHeaders([
                    'x-request-id' => (string) Str::uuid(),
                    'Content-Type' => 'application/json' // Obrigatório na Contabo
                ])
                ->post($url, []); // O array vazio obriga o envio do payload "{}"

            if ($response->successful()) {
                $instancia->status = ($action == 'stop' || $action == 'shutdown') ? 'stopping' : $action . 'ing';
                $instancia->save();
                return back()->with('success', 'Comando enviado com sucesso.');
            }

            // CORREÇÃO: Melhorando o Log para você saber EXATAMENTE o que a API da Contabo respondeu em caso de erro
            Log::error("Erro Contabo na ação '$action': " . $response->body());
            return back()->with('error', 'Falha ao executar a ação: ' . $response->json('message', 'Erro na API.'));

        } catch (\Exception $e) {
            Log::error("Falha na ação '$action' Contabo: " . $e->getMessage());
            return back()->with('error', 'Ocorreu um erro interno.');
        }
    }

public function reinstalarInstancia(Request $request, Contrato $contrato)
{
    if ($contrato->user_id !== Auth::id()) abort(403);

    $request->validate([
        'sistema_id' => 'required|string'
    ]);

    $instancia = $contrato->instancia;
    $accessToken = $this->getAccessToken();
    
    if (!$accessToken) return back()->with('error', 'Falha na autenticação.');

    try {

        $url = $this->apiUrl . '/v1/compute/instances/' . $instancia->instance_id_contabo;

        $userData = "#cloud-config\n" .
        "package_update: true\n" .
        "package_upgrade: true\n" .
        "packages:\n" .
        " - openssh-server\n" .

        "ssh_pwauth: true\n" .
        "disable_root: false\n" .

        "users:\n" .
        " - name: root\n" .
        "   lock_passwd: false\n" .
        " - name: coelhovps\n" .
        "   groups: sudo\n" .
        "   shell: /bin/bash\n" .
        "   sudo: ALL=(ALL) NOPASSWD:ALL\n" .
        "   lock_passwd: false\n" .

        "chpasswd:\n" .
        "  list: |\n" .
        "    root:Coelho12\n" .
        "    coelhovps:Coelho12\n" .
        "  expire: False\n" .

        "runcmd:\n" .
        " - systemctl enable ssh || systemctl enable sshd\n" .
        " - sed -i 's/^#*PasswordAuthentication.*/PasswordAuthentication yes/' /etc/ssh/sshd_config\n" .
        " - sed -i 's/^#*PermitRootLogin.*/PermitRootLogin yes/' /etc/ssh/sshd_config\n" .
        " - sed -i 's/^#*ChallengeResponseAuthentication.*/ChallengeResponseAuthentication no/' /etc/ssh/sshd_config\n" .
        " - sed -i 's/^#*UsePAM.*/UsePAM yes/' /etc/ssh/sshd_config\n" .
        " - mkdir -p /var/run/sshd\n" .
        " - systemctl restart ssh || systemctl restart sshd\n";

        $payload = [
            'imageId' => $request->sistema_id,
            'defaultUser' => 'root',
            'userData' => $userData
        ];

        $instancia->delete();

        $reinstallResponse = Http::withToken($accessToken)
            ->withHeaders([
                'x-request-id' => (string) Str::uuid(),
                'Content-Type' => 'application/json'
            ])
            ->put($url, $payload);

        if ($reinstallResponse->successful()) {

            $instancia->update([
                'root_password' => "Coelho12",
                'status' => 'reinstalling'
            ]);

            return back()->with('success', 'Reinstalação enviada com usuário root + coelhovps configurados.');
        }

        Log::error("Erro Contabo: " . $reinstallResponse->body());
        $msg = $reinstallResponse->json('message') ?? 'Erro desconhecido na API.';
        return back()->with('error', 'Contabo retornou: ' . $msg);

    } catch (\Exception $e) {

        Log::error("Erro Crítico: " . $e->getMessage());
        return back()->with('error', 'Erro interno no servidor.');
    }
}

public function resetPassword(Request $request, Contrato $contrato)
{
    if ($contrato->user_id !== Auth::id()) abort(403);

    if ($contrato->status !== 'ativo') {
        return back()->with('error', 'Contrato suspenso.');
    }

    $request->validate([
        'password' => 'required|string|min:8'
    ]);

    try {

        $instancia = $contrato->instancia;
        $ip = $instancia->ip_address ?? $instancia->ip_v4 ?? $instancia->ip;

        if (!$ip) {
            return back()->with('error', 'IP da VPS não encontrado.');
        }

        while (true) {
            try {
                $ssh = new \phpseclib3\Net\SSH2($ip, 22, 10);
                if ($ssh->login('coelhovps', 'Coelho12')) break;
            } catch (\Exception $e) {}
            sleep(5);
        }

        $novaSenha = $request->password;

        $cmds = [

            "echo 'coelhovps ALL=(ALL) NOPASSWD:ALL' | sudo tee /etc/sudoers.d/coelhovps",

            "sudo mkdir -p /etc/ssh/sshd_config.d",

            "echo '' | sudo tee /etc/ssh/sshd_config.d/99-root.conf",
            "echo 'PermitRootLogin yes' | sudo tee -a /etc/ssh/sshd_config.d/99-root.conf",
            "echo 'PasswordAuthentication yes' | sudo tee -a /etc/ssh/sshd_config.d/99-root.conf",
            "echo 'KbdInteractiveAuthentication yes' | sudo tee -a /etc/ssh/sshd_config.d/99-root.conf",
            "echo 'ChallengeResponseAuthentication yes' | sudo tee -a /etc/ssh/sshd_config.d/99-root.conf",
            "echo 'UsePAM yes' | sudo tee -a /etc/ssh/sshd_config.d/99-root.conf",

            "sudo sed -i '/PermitRootLogin/d' /etc/ssh/sshd_config",
            "sudo sed -i '/PasswordAuthentication/d' /etc/ssh/sshd_config",
            "sudo sed -i '/KbdInteractiveAuthentication/d' /etc/ssh/sshd_config",
            "sudo sed -i '/ChallengeResponseAuthentication/d' /etc/ssh/sshd_config",

            "echo 'PermitRootLogin yes' | sudo tee -a /etc/ssh/sshd_config",
            "echo 'PasswordAuthentication yes' | sudo tee -a /etc/ssh/sshd_config",
            "echo 'KbdInteractiveAuthentication yes' | sudo tee -a /etc/ssh/sshd_config",
            "echo 'ChallengeResponseAuthentication yes' | sudo tee -a /etc/ssh/sshd_config",

            "sudo passwd -u root || true",
            "echo 'root:$novaSenha' | sudo chpasswd",

            "sudo usermod -s /bin/bash root || true",

            "sudo systemctl daemon-reexec || true",
            "sudo systemctl daemon-reload || true",

            "sudo systemctl restart ssh || sudo systemctl restart sshd || sudo service ssh restart || sudo service sshd restart"
        ];

        foreach ($cmds as $c) {
            $ssh->exec($c);
        }

        $instancia->root_password = $novaSenha;
        $instancia->save();

        return back()->with('success', 'Root liberado e senha alterada.');

    } catch (\Exception $e) {

        Log::error('Erro reset senha SSH: ' . $e->getMessage());
        return back()->with('error', 'Erro ao conectar via SSH.');

    }
}



    public function gerenciarSnapshots(Contrato $contrato)
    {
        if ($contrato->user_id !== Auth::id()) abort(403);
        if ($contrato->status !== 'ativo') return redirect()->route('painel.dashboard')->with('error', 'Acesso bloqueado.');

        $instancia = $contrato->instancia;
        $accessToken = $this->getAccessToken();
        if (!$accessToken) return back()->with('error', 'Erro de autenticação.');

        try {
            $url = $this->apiUrl . '/v1/compute/instances/' . $instancia->instance_id_contabo . '/snapshots';
            $response = Http::withToken($accessToken)->withHeaders(['x-request-id' => (string) Str::uuid()])->get($url);
            $snapshots = $response->successful() ? $response->json('data') : [];
            return view('painel.snapshots', compact('contrato', 'snapshots'));
        } catch (\Exception $e) {
            return back()->with('error', 'Erro ao buscar snapshots.');
        }
    }

    public function createSnapshot(Request $request, Contrato $contrato)
    {
        if ($contrato->user_id !== Auth::id() || $contrato->status !== 'ativo') abort(403);
        $request->validate(['name' => 'required|string|max:30']);
        
        $accessToken = $this->getAccessToken();
        $url = $this->apiUrl . '/v1/compute/instances/' . $contrato->instancia->instance_id_contabo . '/snapshots';
        
        $response = Http::withToken($accessToken)->withHeaders(['x-request-id' => (string) Str::uuid()])->post($url, [
            'name' => $request->name,
            'description' => $request->description ?? ' '
        ]);
        
        if ($response->status() == 201) return back()->with('success', 'Snapshot criado.');
        return back()->with('error', 'Falha ao criar snapshot.');
    }

    public function deleteSnapshot(Contrato $contrato, $snapshotId)
    {
        if ($contrato->user_id !== Auth::id() || $contrato->status !== 'ativo') abort(403);
        $accessToken = $this->getAccessToken();
        $url = $this->apiUrl . '/v1/compute/instances/' . $contrato->instancia->instance_id_contabo . '/snapshots/' . $snapshotId;
        $response = Http::withToken($accessToken)->withHeaders(['x-request-id' => (string) Str::uuid()])->delete($url);
        
        return $response->status() == 204 
            ? back()->with('success', 'Removido.') 
            : back()->with('error', 'Falha ao remover snapshot.');
    }

    public function revertSnapshot(Contrato $contrato, $snapshotId)
    {
        if ($contrato->user_id !== Auth::id() || $contrato->status !== 'ativo') abort(403);
        $accessToken = $this->getAccessToken();
        $url = $this->apiUrl . '/v1/compute/instances/' . $contrato->instancia->instance_id_contabo . '/snapshots/' . $snapshotId . '/rollback';
        $response = Http::withToken($accessToken)->withHeaders(['x-request-id' => (string) Str::uuid()])->post($url);
        return $response->successful() ? back()->with('success', 'Restaurando...') : back()->with('error', 'Falha.');
    }
}