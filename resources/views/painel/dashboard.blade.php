@extends('painel.layouts.app')

@section('title', 'Dashboard')

@push('styles')
<style>
    :root { --primary: #38bdf8; }
    .dash-card { background: rgba(12, 12, 12, 0.88); backdrop-filter: blur(14px); border: 1px solid rgba(255, 255, 255, 0.06); }
    .no-overflow { overflow: visible !important; }
    .status-pill { font-weight: 800; text-transform: uppercase; letter-spacing: 0.05em; font-size: 10px; }
    [x-cloak] { display: none !important; }

    /* Estilos Reinstalação */
    .bg-black-solid { background: #000000 !important; }
    .os-tab-re { cursor: pointer; padding: 12px 15px; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); font-weight: 700; transition: 0.3s; text-align: center; font-size: 11px; color: #71717a; text-transform: uppercase; }
    .os-tab-re.active { background: var(--primary); color: #000; }
    .selectable-card { cursor: pointer; border: 2px solid rgba(255,255,255,.05); transition: all 0.3s ease; background: rgba(255,255,255,0.01); }
    .selectable-card.selected { border-color: var(--primary) !important; background: rgba(56,189,248, 0.05); }
    .check-indicator { width: 18px; height: 18px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; }
    .selected .check-indicator { background: var(--primary); border-color: var(--primary); }
    .hidden-radio { display: none; }
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 10px; }
</style>
@endpush

@section('content')
<div class="space-y-10 no-overflow">
    <div class="flex flex-col md:flex-row md:items-end justify-between gap-4" data-aos="fade-right">
        <div>
            <h1 class="text-3xl md:text-4xl font-black text-white italic uppercase tracking-tight">Meu <span class="text-sky-400">Painel</span></h1>
            <p class="text-xs font-bold uppercase tracking-[0.2em] text-zinc-500 mt-2">Olá, <span class="text-white">{{ Auth::user()->name }}</span> • Status: {{ Auth::user()->status ?? 'Ativo' }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="dash-card rounded-[2rem] p-8">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Servidores Ativos</p>
            <div class="flex items-end justify-between mt-2">
                <p class="text-5xl font-black text-white tracking-tighter">{{ $servidoresAtivos }}</p>
                <div class="h-12 w-12 rounded-2xl bg-sky-400/10 flex items-center justify-center text-sky-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2H5z"/></svg>
                </div>
            </div>
        </div>
        <div class="dash-card rounded-[2rem] p-8">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Pendências</p>
            <div class="flex items-end justify-between mt-2">
                <p class="text-5xl font-black text-white tracking-tighter">{{ $faturasPendentes }}</p>
                <div class="h-12 w-12 rounded-2xl bg-white/5 flex items-center justify-center text-zinc-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
            </div>
        </div>
        <div class="dash-card rounded-[2rem] p-8">
            <p class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-500">Suporte</p>
            <div class="flex items-end justify-between mt-2">
                <p class="text-5xl font-black text-white tracking-tighter">{{ $tickets_abertos }}</p>
                <div class="h-12 w-12 rounded-2xl bg-white/5 flex items-center justify-center text-zinc-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2V7a2 2 0 012-2h3.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V8z"/></svg>
                </div>
            </div>
        </div>
    </div>

    <div class="dash-card rounded-[2.5rem] no-overflow shadow-2xl">
        <div class="px-8 py-6 border-b border-white/5 flex items-center justify-between">
            <h2 class="text-lg font-black text-white italic uppercase tracking-tight">Meus <span class="text-sky-400">Serviços</span></h2>
            <a href="/" class="px-4 py-2 rounded-xl bg-white/5 text-[10px] font-black uppercase text-sky-400 hover:bg-sky-400 hover:text-black transition">Contratar +</a>
        </div>
        
        <div class="no-overflow">
            <table class="min-w-full divide-y divide-white/5 no-overflow">
                <thead class="bg-white/[0.02] hidden md:table-header-group">
                    <tr>
                        <th class="py-4 pl-8 text-left text-[10px] font-black uppercase text-zinc-600">Servidor</th>
                        <th class="px-3 py-4 text-left text-[10px] font-black uppercase text-zinc-600">Status</th>
                        <th class="px-3 py-4 text-left text-[10px] font-black uppercase text-zinc-600">Acesso</th>
                        <th class="py-4 pr-8 text-right text-[10px] font-black uppercase text-zinc-600">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 no-overflow">
                    @forelse ($contratos as $contrato)
                        <tr class="flex flex-col md:table-row py-6 md:py-0 transition-colors hover:bg-white/[0.01] no-overflow">
                            <td class="px-8 py-2 md:py-6">
                                <div class="flex flex-col">
                                    <span class="text-sm font-black text-white italic uppercase">VPS {{ $contrato->vCPU }}</span>
                                    <span class="text-[10px] font-bold text-zinc-600 uppercase mt-1">Contrato #{{ $contrato->id }}</span>
                                </div>
                            </td>

                            <td class="px-8 md:px-3 py-2 md:py-6">
                                @php
                                    $status = $contrato->status === 'suspenso' ? 'suspenso' : ($contrato->instancia->status ?? $contrato->status);
                                    $statusClasses = [
                                        'running' => 'bg-emerald-400/10 text-emerald-400', 
                                        'ativo' => 'bg-emerald-400/10 text-emerald-400',
                                        'suspenso' => 'bg-red-500/10 text-red-500',
                                        'stopped' => 'bg-zinc-800 text-zinc-500'
                                    ];
                                @endphp
                                <span class="status-pill inline-flex items-center rounded-lg px-3 py-1 {{ $statusClasses[$status] ?? 'bg-sky-400/10 text-sky-400 animate-pulse' }}">
                                    {{ str_replace('_', ' ', $status) }}
                                </span>
                            </td>

                            <td class="px-8 md:px-3 py-2 md:py-6" x-data="{ show: false }">
                                @if($contrato->instancia)
                                    <div class="flex flex-col gap-1">
                                        <div class="text-sm font-bold text-zinc-300"><span class="text-[9px] text-zinc-600 uppercase mr-1">IP:</span> {{ $contrato->instancia->ip_v4 }}</div>
                                        <div class="flex items-center gap-2">
                                            <span class="text-[9px] text-zinc-600 uppercase">Senha:</span>
                                            <span class="text-sm font-bold text-zinc-300" :class="show ? '' : 'blur-[5px] select-none'" x-text="show ? '{{ $contrato->instancia->root_password }}' : '••••••••'"></span>
                                            <button @click="show = !show" class="text-zinc-600 hover:text-sky-400 transition outline-none">
                                                <svg x-show="!show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                                <svg x-show="show" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.542-7a10.024 10.024 0 014.132-5.411m0 0L21.243 4.243m-4.242 4.242L9.88 15.88M1 1l22 22"/></svg>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[10px] text-zinc-700 font-bold uppercase italic tracking-widest">Provisionando...</span>
                                @endif
                            </td>

                            <td class="px-8 md:px-0 py-4 md:py-6 md:pr-8 md:text-right no-overflow">
                                <div class="relative inline-block" x-data="{ open: false }" @click.away="open = false">
                                    <button @click="open = !open" type="button" class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-6 py-3 rounded-2xl bg-sky-400 text-black text-[10px] font-black uppercase tracking-widest transition hover:bg-white shadow-lg shadow-sky-400/20">
                                        Gerenciar
                                        <svg class="h-4 w-4 transition-transform" :class="open ? 'rotate-180' : ''" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" /></svg>
                                    </button>
                                    
                                    <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
                                         class="absolute right-0 bottom-full mb-3 z-[100] w-60 rounded-3xl bg-[#0c0c0c] border border-white/10 shadow-2xl py-3 overflow-hidden">
                                        
                                        @if($contrato->status === 'suspenso')
                                            <div class="px-6 py-4 text-center">
                                                <p class="text-[10px] font-black text-red-500 uppercase italic">Contrato Suspenso</p>
                                                <p class="text-[9px] text-zinc-500 uppercase mt-1">Aguardando Pagamento</p>
                                            </div>
                                        @elseif($contrato->instancia && $contrato->instancia->instance_id_contabo)
                                            @if($contrato->instancia->status == 'running')
                                                <button class="action-btn w-full px-6 py-3 text-left text-[11px] font-bold text-red-400 hover:bg-red-400/10 flex items-center gap-3 transition" @click="open = false"
                                                    data-url="{{ route('painel.contrato.action', ['contrato' => $contrato, 'action' => 'stop']) }}" data-title="Desligar Servidor" data-text="A instância será interrompida." data-btn-text="Desligar" data-btn-class="bg-red-500">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/><rect x="9" y="9" width="6" height="6" rx="1"/></svg> Parar Servidor
                                                </button>
                                            @else
                                                <button class="action-btn w-full px-6 py-3 text-left text-[11px] font-bold text-emerald-400 hover:bg-emerald-400/10 flex items-center gap-3 transition" @click="open = false"
                                                    data-url="{{ route('painel.contrato.action', ['contrato' => $contrato, 'action' => 'start']) }}" data-title="Ligar Servidor" data-text="Iniciar VPS?" data-btn-text="Ligar" data-btn-class="bg-emerald-500 text-black">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg> Iniciar Servidor
                                                </button>
                                            @endif

                                            <button class="action-btn w-full px-6 py-3 text-left text-[11px] font-bold text-orange-400 hover:bg-orange-400/10 flex items-center gap-3 transition" @click="open = false"
                                                data-url="{{ route('painel.contrato.action', ['contrato' => $contrato, 'action' => 'restart']) }}" data-title="Reiniciar Servidor" data-text="Confirmar reboot?" data-btn-text="Reiniciar" data-btn-class="bg-orange-500">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Reiniciar
                                            </button>

                                            <a href="{{ route('painel.contrato.snapshots.gerenciar', $contrato->id) }}" class="w-full px-6 py-3 text-left text-[11px] font-bold text-zinc-300 hover:bg-white/5 flex items-center gap-3 transition">
                                                <svg class="h-4 w-4 text-sky-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg> Snapshots
                                            </a>

                                            <button class="reset-password-modal-btn w-full px-6 py-3 text-left text-[11px] font-bold text-zinc-300 hover:bg-white/5 flex items-center gap-3 transition" @click="open = false" data-url="{{ route('painel.contrato.reset_password', $contrato->id) }}">
                                                <svg class="h-4 w-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m-2-2a2 2 0 00-2 2m2-2V5a2 2 0 10-4 0v2m4 0h-4m-6 3a2 2 0 114 0 2 2 0 01-4 0z"/></svg> Trocar Senha
                                            </button>

                                            <button class="reinstall-modal-btn w-full px-6 py-3 text-left text-[11px] font-bold text-zinc-300 hover:bg-white/5 flex items-center gap-3 transition" @click="open = false" data-url="{{ route('painel.contrato.reinstalar', $contrato->id) }}">
                                                <svg class="h-4 w-4 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg> Reinstalar OS
                                            </button>

                                            <a href="{{ route('painel.terminal.show', $contrato->id) }}" target="_blank" class="w-full px-6 py-4 text-left text-[11px] font-black text-white hover:bg-sky-400 hover:text-black flex items-center gap-3 transition border-t border-white/5 uppercase tracking-widest">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2-2v14a2 2 0 002 2z"/></svg> Terminal
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="py-20 text-center text-zinc-700 italic font-bold uppercase text-[10px] tracking-widest">Nenhum servidor encontrado</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL CONFIRMAÇÃO AÇÕES SIMPLES --}}
<div id="confirmModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black hidden p-6">
    <div class="dash-card rounded-[2.5rem] w-full max-w-lg overflow-hidden border-sky-400/20 bg-black-solid">
        <div class="p-10">
            <h2 id="confirmModalTitle" class="text-2xl font-black text-white italic uppercase mb-2"></h2>
            <p id="confirmModalText" class="text-sm text-zinc-500 leading-relaxed"></p>
        </div>
        <div class="p-8 bg-white/[0.02] border-t border-white/5 flex gap-4">
            <button type="button" class="modal-cancel-btn flex-1 py-4 rounded-2xl border border-white/10 text-white text-[10px] font-black uppercase">Voltar</button>
            <form id="confirmForm" method="POST" action="" class="flex-1">@csrf
                <button id="confirmModalButton" type="submit" class="w-full py-4 rounded-2xl text-[10px] font-black uppercase tracking-widest transition text-white"><span class="btn-text">Confirmar</span></button>
            </form>
        </div>
    </div>
</div>

{{-- MODAL REINSTALAÇÃO --}}
<div id="reinstallModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black hidden p-4 overflow-y-auto">
    <div class="dash-card rounded-[3rem] w-full max-w-2xl border-yellow-500/20 my-auto bg-black-solid">
        <div class="p-8 md:p-10">
            <h2 class="text-2xl font-black text-white italic uppercase mb-2">Formatar <span class="text-yellow-500">Servidor</span></h2>
            <p class="text-[10px] text-zinc-500 font-bold uppercase mb-8 tracking-widest italic">CUIDADO: Isso apagará todos os dados do disco.</p>
            
            <form id="reinstallForm" method="POST" action="">
                @csrf
                
                <div class="mb-10">
                    <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-4">Novo Sistema Operacional</label>
                    
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach ($sistemasAgrupados as $familia => $versoes)
                            <div class="os-tab-re flex-1 min-w-[100px]" data-target="re-v-{{ Str::slug($familia) }}">
                                {{ $familia }}
                            </div>
                        @endforeach
                    </div>

                    @foreach ($sistemasAgrupados as $familia => $versoes)
                    <div id="re-v-{{ Str::slug($familia) }}" class="os-content-re hidden">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                            @foreach ($versoes as $versao)
                                <label class="selectable-card p-4 rounded-xl flex items-center justify-between transition-all">
                                    <span class="text-white text-xs font-bold">{{ $versao->description }}</span>
                                    <div class="check-indicator">
                                        <svg class="w-2.5 h-2.5 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <input type="radio" name="sistema_id" value="{{ $versao->image_id }}" class="hidden-radio" required>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="flex gap-4">
                    <button type="button" class="modal-cancel-btn flex-1 py-4 rounded-2xl border border-white/10 text-white text-[10px] font-black uppercase">Voltar</button>
                    <button type="submit" class="flex-1 py-4 rounded-2xl bg-red-600 text-white text-[10px] font-black uppercase shadow-lg shadow-red-600/20">Confirmar Formatação</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL TROCA DE SENHA --}}
<div id="resetPasswordModal" class="fixed inset-0 z-[200] flex items-center justify-center bg-black hidden p-6">
    <div class="dash-card rounded-[3rem] w-full max-w-lg border-sky-400/20 bg-black-solid">
        <div class="p-10">
            <h2 class="text-2xl font-black text-white italic uppercase mb-2">Alterar <span class="text-sky-400">Acesso</span></h2>
            <form id="resetPasswordForm" method="POST" action="">@csrf
                <label class="text-[9px] font-black uppercase text-zinc-600 tracking-widest block mb-2">Nova Senha</label>
                <input name="password" type="password" required minlength="8" class="w-full py-4 px-5 rounded-2xl bg-white/5 border border-white/10 text-white focus:outline-none focus:border-sky-400" placeholder="Digite a nova senha">
                <div class="mt-8 flex gap-4">
                    <button type="button" class="modal-cancel-btn flex-1 py-4 rounded-2xl border border-white/10 text-white text-[10px] font-black uppercase">Voltar</button>
                    <button type="submit" class="flex-1 py-4 rounded-2xl bg-sky-400 text-black text-[10px] font-black uppercase">Atualizar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    // Ações de Botões Simples
    const confirmModal = document.getElementById('confirmModal');
    const confirmForm = document.getElementById('confirmForm');
    const confirmModalTitle = document.getElementById('confirmModalTitle');
    const confirmModalText = document.getElementById('confirmModalText');
    const confirmModalButton = document.getElementById('confirmModalButton');
    const confirmBtnText = confirmModalButton.querySelector('.btn-text');

    document.querySelectorAll('.action-btn').forEach(button => {
        button.addEventListener('click', () => {
            confirmModalTitle.textContent = button.dataset.title;
            confirmModalText.textContent = button.dataset.text;
            confirmBtnText.textContent = button.dataset.btnText;
            confirmModalButton.className = 'w-full py-4 rounded-2xl text-[10px] font-black uppercase transition ' + button.dataset.btnClass;
            confirmForm.action = button.dataset.url;
            confirmModal.classList.remove('hidden');
        });
    });

    // Lógica das Abas de Reinstalação
    const reTabs = document.querySelectorAll('.os-tab-re');
    const reContents = document.querySelectorAll('.os-content-re');

    function refreshReSelection() {
        document.querySelectorAll('#reinstallModal .selectable-card').forEach(card => {
            card.classList.toggle('selected', card.querySelector('input').checked);
        });
    }

    reTabs.forEach(tab => {
        tab.addEventListener('click', () => {
            reTabs.forEach(t => t.classList.remove('active'));
            tab.classList.add('active');
            const target = tab.dataset.target;
            reContents.forEach(c => c.classList.toggle('hidden', c.id !== target));
        });
    });

    document.querySelectorAll('#reinstallModal .selectable-card').forEach(card => {
        card.addEventListener('click', () => {
            card.querySelector('input').checked = true;
            refreshReSelection();
        });
    });

    document.querySelectorAll('.reinstall-modal-btn').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('reinstallForm').action = button.dataset.url;
            document.getElementById('reinstallModal').classList.remove('hidden');
            if(reTabs.length > 0) reTabs[0].click();
        });
    });

    // Outros Modais
    document.querySelectorAll('.reset-password-modal-btn').forEach(button => {
        button.addEventListener('click', () => {
            document.getElementById('resetPasswordForm').action = button.dataset.url;
            document.getElementById('resetPasswordModal').classList.remove('hidden');
        });
    });

    document.querySelectorAll('.modal-cancel-btn').forEach(button => {
        button.addEventListener('click', () => button.closest('.fixed.inset-0').classList.add('hidden'));
    });
});
</script>
@endsection