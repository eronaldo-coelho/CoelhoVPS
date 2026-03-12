<?php

namespace App\Http\Controllers\Painel;

use App\Http\Controllers\Controller;
use App\Models\Contrato;
use App\Models\Ticket;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contrato_id' => 'required|exists:contratos,id',
            'motivo_fixo' => 'required|string|max:255',
            'explicacao' => 'nullable|string',
        ]);

        $contrato = Contrato::findOrFail($validated['contrato_id']);
        if ($contrato->user_id !== auth()->id()) {
            return response()->json(['message' => 'Acesso não autorizado.'], 403);
        }

        $motivoCompleto = $validated['motivo_fixo'];
        if (!empty($validated['explicacao'])) {
            $motivoCompleto .= "\n\nExplicação adicional do cliente:\n" . $validated['explicacao'];
        }

        Ticket::create([
            'user_id' => auth()->id(),
            'contrato_id' => $validated['contrato_id'],
            'motivo' => $motivoCompleto,
            'resolvido' => false,
        ]);

        return response()->json(['message' => 'Ticket criado com sucesso!'], 201);
    }
}