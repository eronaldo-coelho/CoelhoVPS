@extends('painel.layouts.app')

@section('title', 'Gerenciar Snapshots')

@push('styles')
<style>
    :root { --primary: #38bdf8; }
    
    .dash-card {
        background: rgba(12, 12, 12, 0.88);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .campo-input {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        transition: all 0.3s ease;
    }

    .campo-input:focus {
        border-color: var(--primary);
        background: rgba(56, 189, 248, 0.03);
        box-shadow: 0 0 15px rgba(56, 189, 248, 0.1);
        outline: none;
    }
</style>
@endpush

@section('content')
<div class="space-y-10">
    
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6" data-aos="fade-right">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-white italic uppercase tracking-tight">
                Gerenciar <span class="text-sky-400">Snapshots</span>
            </h1>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500 mt-2">
                Instância: <span class="text-zinc-300">VPS {{ $contrato->vCPU }} Core</span> • Contrato #{{ $contrato->id }}
            </p>
        </div>
        <a href="{{ route('painel.dashboard') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-2xl border border-white/10 text-[10px] font-black uppercase tracking-widest text-zinc-400 hover:bg-white hover:text-black transition shadow-lg">
            ← Voltar ao Início
        </a>
    </div>

    <!-- ALERTS -->
    @if (session('success'))
        <div class="rounded-2xl bg-sky-400/10 border border-sky-400/20 p-4" data-aos="fade-left">
            <p class="text-sm font-bold text-sky-400 uppercase tracking-tight italic">{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-2xl bg-red-500/10 border border-red-500/20 p-4" data-aos="fade-left">
            <p class="text-sm font-bold text-red-500 uppercase tracking-tight italic">{{ session('error') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        <!-- CRIAR SNAPSHOT -->
        <div class="lg:col-span-4" data-aos="fade-up" data-aos-delay="100">
            <div class="dash-card rounded-[2.5rem] p-8 md:p-10 sticky top-32">
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-10 w-10 rounded-xl bg-sky-400/10 flex items-center justify-center text-sky-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    </div>
                    <h3 class="text-lg font-black text-white italic uppercase">Novo Ponto</h3>
                </div>

                <form action="{{ route('painel.contrato.snapshots.create', $contrato->id) }}" method="POST" class="space-y-6">
                    @csrf
                    <div>
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Identificação do Snapshot</label>
                        <input type="text" name="name" required maxlength="30" class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-700" placeholder="Ex: Antes de Atualizar">
                    </div>
                    <div>
                        <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Breve Descrição</label>
                        <input type="text" name="description" maxlength="255" class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-700" placeholder="Opcional">
                    </div>
                    <div class="pt-4">
                        <button type="submit" class="w-full py-5 rounded-2xl bg-sky-400 text-black font-black uppercase tracking-[0.2em] text-[10px] transition transform hover:scale-[1.02] hover:bg-white active:scale-95 shadow-xl shadow-sky-400/10">
                            Criar Agora
                        </button>
                    </div>
                </form>
                <p class="mt-6 text-[9px] text-center text-zinc-600 font-bold uppercase tracking-widest leading-relaxed">
                    O servidor poderá ficar brevemente<br>inacessível durante o congelamento.
                </p>
            </div>
        </div>

        <!-- LISTA DE SNAPSHOTS -->
        <div class="lg:col-span-8" data-aos="fade-up" data-aos-delay="200">
            <div class="dash-card rounded-[2.5rem] overflow-hidden">
                <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between bg-white/[0.01]">
                    <h3 class="text-sm font-black text-white italic uppercase tracking-widest">Pontos de Restauração Ativos</h3>
                    <span class="text-[9px] font-black uppercase text-zinc-500 bg-white/5 px-3 py-1 rounded-full">{{ count($snapshots) }} Slot(s) ocupado(s)</span>
                </div>

                <div class="divide-y divide-white/5">
                    @forelse ($snapshots as $snapshot)
                        <div class="p-8 hover:bg-white/[0.01] transition-colors flex flex-col md:flex-row md:items-center justify-between gap-6">
                            <div class="space-y-1">
                                <div class="flex items-center gap-3">
                                    <span class="text-lg font-black text-white uppercase italic tracking-tight">{{ $snapshot['name'] }}</span>
                                    <span class="text-[9px] px-2 py-0.5 rounded-md bg-sky-400/10 text-sky-400 font-black uppercase">Ready</span>
                                </div>
                                <p class="text-sm text-zinc-500">{{ $snapshot['description'] ?: 'Sem descrição técnica adicional.' }}</p>
                                <div class="flex items-center gap-4 mt-3">
                                    <div class="flex items-center gap-1.5">
                                        <svg class="w-3.5 h-3.5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        <span class="text-[10px] font-bold text-zinc-600 uppercase">{{ \Carbon\Carbon::parse($snapshot['createdDate'])->format('d/m/Y H:i') }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <form action="{{ route('painel.contrato.snapshots.revert', [$contrato->id, $snapshot['snapshotId']]) }}" method="POST" 
                                    onsubmit="return confirm('ATENÇÃO: A restauração irá apagar todos os dados atuais e reverter o servidor para este estado exato. Esta operação é IRREVERSÍVEL. Confirmar?');">
                                    @csrf
                                    <button type="submit" class="px-5 py-3 rounded-xl bg-white/5 border border-white/10 text-orange-400 text-[10px] font-black uppercase tracking-widest hover:bg-orange-500 hover:text-white transition active:scale-95">
                                        Restaurar
                                    </button>
                                </form>

                                <form action="{{ route('painel.contrato.snapshots.delete', [$contrato->id, $snapshot['snapshotId']]) }}" method="POST" 
                                    onsubmit="return confirm('Deseja excluir permanentemente este snapshot?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-5 py-3 rounded-xl bg-white/5 border border-white/10 text-red-500 text-[10px] font-black uppercase tracking-widest hover:bg-red-500 hover:text-white transition active:scale-95">
                                        Deletar
                                    </button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="py-24 text-center">
                            <div class="inline-flex items-center justify-center w-16 h-16 bg-white/5 rounded-full mb-4 text-zinc-800">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.58 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.58 4 8 4s8-1.79 8-4M4 7c0-2.21 3.58-4 8-4s8 1.79 8 4m0 5c0 2.21-3.58 4-8 4s-8-1.79-8-4"/></svg>
                            </div>
                            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-700 italic">Nenhum snapshot armazenado no cluster</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection