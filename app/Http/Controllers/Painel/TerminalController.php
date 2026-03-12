<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use Illuminate\Support\Facades\Auth;

class TerminalController extends Controller
{
    public function show(Contrato $contrato)
    {
        if ($contrato->user_id !== Auth::id()) {
            abort(403, 'Acesso não autorizado.');
        }

        $instancia = $contrato->instancia()->firstOrFail();

        // Parâmetros para o WebSocket
        $queryParams = http_build_query([
            'ip' => $instancia->ip_v4,
            'password' => "Coelho12",
        ]);
        
        $protocol = request()->isSecure() ? 'wss://' : 'ws://';
        $websocketHost = request()->getHost();
        
        // A URL deve apontar para o local configurado no Nginx (/websocket/)
        $websocketUrl = $protocol . $websocketHost . "/websocket/?" . $queryParams;

        return view('painel.terminal', [
            'websocketUrl' => $websocketUrl,
            'contrato' => $contrato,
            'instancia' => $instancia,
        ]);
    }
}