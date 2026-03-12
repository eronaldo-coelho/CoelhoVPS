@extends('painel.layouts.app')

@section('title', 'Meus Cartões')

@push('styles')
<style>
    :root { --primary: #38bdf8; }
    
    .dash-card {
        background: rgba(12, 12, 12, 0.88);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.06);
        transition: .3s ease;
    }

    .credit-card-item {
        background: linear-gradient(135deg, rgba(255,255,255,0.05) 0%, rgba(255,255,255,0.01) 100%);
        border: 1px solid rgba(255,255,255,0.1);
        position: relative;
        overflow: hidden;
    }

    .credit-card-item:hover {
        border-color: var(--primary);
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(56, 189, 248, 0.1);
    }

    .chip-icon {
        width: 40px;
        height: 30px;
        background: linear-gradient(135deg, #ffd700 0%, #b8860b 100%);
        border-radius: 6px;
        opacity: 0.8;
    }
</style>
@endpush

@section('content')
<div class="space-y-10">
    
    <!-- HEADER -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6" data-aos="fade-right">
        <div>
            <h1 class="text-4xl font-black text-white italic uppercase tracking-tight">
                Meus <span class="text-sky-400">Cartões</span>
            </h1>
            <p class="text-[10px] font-bold uppercase tracking-[0.2em] text-zinc-500 mt-2">
                Gerencie seus métodos de pagamento salvos com segurança
            </p>
        </div>

        @if($cliente)
            <a href="{{ route('cartao.create') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-sky-400 text-black text-[10px] font-black uppercase tracking-widest transition transform hover:scale-105 hover:bg-white active:scale-95 shadow-xl shadow-sky-400/20">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4"/></svg>
                Novo Cartão
            </a>
        @else
            <a href="{{ route('cliente.create') }}" class="inline-flex items-center gap-2 px-8 py-4 rounded-2xl bg-orange-500 text-white text-[10px] font-black uppercase tracking-widest transition transform hover:scale-105 active:scale-95 shadow-xl shadow-orange-500/20">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                Completar Cadastro
            </a>
        @endif
    </div>

    <!-- FEEDBACKS -->
    @if (session('success'))
        <div class="rounded-2xl bg-sky-400/10 border border-sky-400/20 p-4" data-aos="fade-up">
            <p class="text-xs font-black text-sky-400 uppercase tracking-widest italic">{{ session('success') }}</p>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-2xl bg-red-500/10 border border-red-500/20 p-4" data-aos="fade-up">
            <p class="text-xs font-black text-red-500 uppercase tracking-widest italic">{{ session('error') }}</p>
        </div>
    @endif

    <!-- LISTA DE CARTÕES -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" data-aos="fade-up" data-aos-delay="200">
        @forelse ($cards as $card)
            <div class="dash-card credit-card-item rounded-[2.5rem] p-8 flex flex-col justify-between min-h-[220px]">
                
                <div class="flex justify-between items-start">
                    <div class="chip-icon"></div>
                    <img src="{{ str_replace('http://', 'https://', $card->payment_method->thumbnail) }}" alt="{{ $card->payment_method->name }}" class="h-8 grayscale brightness-200">
                </div>

                <div class="mt-8">
                    <p class="text-[10px] font-black uppercase text-zinc-600 tracking-[0.3em] mb-2">Número do Cartão</p>
                    <div class="flex gap-4 text-xl font-black text-white italic tracking-tighter">
                        <span>••••</span>
                        <span>••••</span>
                        <span>••••</span>
                        <span class="text-sky-400">{{ $card->last_four_digits }}</span>
                    </div>
                </div>

                <div class="mt-8 flex justify-between items-end">
                    <div class="flex flex-col">
                        <span class="text-[9px] font-black uppercase text-zinc-600 tracking-widest">Validade</span>
                        <span class="text-sm font-bold text-zinc-300">{{ str_pad($card->expiration_month, 2, '0', STR_PAD_LEFT) }}/{{ substr($card->expiration_year, -2) }}</span>
                    </div>

                    <form action="{{ route('painel.cartoes.destroy', $card->id) }}" method="POST" 
                        onsubmit="return confirm('ATENÇÃO: Deseja realmente remover este método de pagamento?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-3 rounded-xl bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition active:scale-90" title="Remover Cartão">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        @empty
            <div class="col-span-full py-24 flex flex-col items-center justify-center dash-card rounded-[3rem] border-dashed">
                <div class="w-20 h-20 rounded-full bg-white/5 flex items-center justify-center text-zinc-800 mb-6">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                </div>
                <h3 class="text-xl font-black text-zinc-600 uppercase italic tracking-tight">Nenhum cartão vinculado</h3>
                <p class="text-sm text-zinc-700 mt-2">Vincule um cartão para renovações automáticas.</p>
                
                @if($cliente)
                    <a href="{{ route('cartao.create') }}" class="mt-8 text-sky-400 font-black uppercase text-[10px] tracking-[0.2em] hover:text-white transition">
                        + Clique para Adicionar
                    </a>
                @endif
            </div>
        @endforelse
    </div>

    <!-- SECURITY INFO -->
    <div class="mt-12 p-8 bg-sky-400/5 rounded-[2rem] border border-sky-400/10 flex flex-col md:flex-row items-center gap-6" data-aos="fade-up">
        <div class="h-12 w-12 rounded-2xl bg-sky-400/20 flex items-center justify-center text-sky-400 flex-shrink-0">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
        </div>
        <div>
            <h4 class="text-white font-black italic uppercase tracking-tight">Segurança dos Dados</h4>
            <p class="text-xs text-zinc-500 leading-relaxed mt-1">
                A CoelhoVPS não armazena o número completo do seu cartão em nossos servidores. Todos os dados sensíveis são processados via Token Criptografado pelo gateway de pagamento certificado PCI-DSS.
            </p>
        </div>
    </div>
</div>
@endsection