<?php

namespace App\Http\Controllers;

use App\Models\Servidor;
use App\Models\ServidorApi;
use App\Models\Regiao;
use App\Models\Sistema;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ServidorController extends Controller
{
    public function index()
    {
        $servidores = Servidor::all();
        return view('inicio', ['servidores' => $servidores]);
    }

    public function show(Servidor $servidor)
    {
        $opcoesDisco = ServidorApi::where('servidor_id', $servidor->id)->get();
        $regioes = Regiao::all();
        $configuracaoSalva = session('configuracao_pendente', []);
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
                if (Str::startsWith($item->name, $prefix)) {
                    return $name;
                }
            }
            return 'Outros';
        });
        return view('servidor-detalhes', [
            'servidor' => $servidor,
            'opcoesDisco' => $opcoesDisco,
            'regioes' => $regioes,
            'sistemasAgrupados' => $sistemasAgrupados,
            'configuracaoSalva' => $configuracaoSalva
        ]);
    }
}