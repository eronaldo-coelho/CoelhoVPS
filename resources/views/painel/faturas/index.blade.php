@extends('painel.layouts.app')

@section('title', 'Minhas Faturas')

@push('styles')
<style>
    :root { --primary: #38bdf8; }
    .selectable-card { cursor: pointer; transition: all 0.2s ease-in-out; border: 2px solid rgba(255,255,255,0.05); }
    .selectable-card:hover { transform: translateY(-2px); border-color: var(--primary); }
    .selected { border-color: var(--primary) !important; background: rgba(56, 189, 248, 0.05) !important; box-shadow: 0 0 15px rgba(56, 189, 248, 0.1); }
    .hidden-radio { display: none; }
    
    .fatura-card {
        background: rgba(12, 12, 12, 0.88);
        backdrop-filter: blur(14px);
        border: 1px solid rgba(255, 255, 255, 0.06);
    }
</style>
@endpush

@section('content')
<div x-data="faturasPage()">

    <div data-aos="fade-right">
        <h1 class="text-4xl font-black text-white italic uppercase tracking-tight">Minhas <span class="text-sky-400">Faturas</span></h1>
        <p class="text-xs font-bold uppercase tracking-[0.2em] text-zinc-500 mt-2">Gerencie seu histórico financeiro e pendências</p>
    </div>

    <!-- FATURAS PENDENTES -->
    <div class="mt-12" data-aos="fade-up" data-aos-delay="200">
        <div class="mb-6 flex items-center gap-3">
            <span class="h-2 w-2 rounded-full bg-sky-400 animate-pulse"></span>
            <h2 class="text-lg font-black text-white italic uppercase">Pendentes de Pagamento</h2>
        </div>
        
        <div class="overflow-hidden rounded-[2rem] fatura-card shadow-2xl shadow-sky-400/5">
            <div class="min-w-full">
                <div class="hidden bg-white/5 md:grid md:grid-cols-12 md:gap-4 border-b border-white/5">
                    <div class="py-4 pl-8 text-left text-[10px] font-black uppercase tracking-widest text-zinc-500 md:col-span-2">ID #</div>
                    <div class="py-4 px-3 text-left text-[10px] font-black uppercase tracking-widest text-zinc-500 md:col-span-3">Serviço Contratado</div>
                    <div class="py-4 px-3 text-left text-[10px] font-black uppercase tracking-widest text-zinc-500 md:col-span-2">Vencimento</div>
                    <div class="py-4 px-3 text-left text-[10px] font-black uppercase tracking-widest text-zinc-500 md:col-span-2">Valor</div>
                    <div class="py-4 pr-8 md:col-span-3"></div>
                </div>
                
                <div class="divide-y divide-white/5">
                    @forelse ($faturasPendentes as $fatura)
                        <div class="grid grid-cols-1 gap-y-3 p-6 transition-colors hover:bg-white/[0.02] md:grid-cols-12 md:gap-4 md:p-0 md:items-center">
                            <div class="flex justify-between md:col-span-2 md:block md:py-6 md:pl-8">
                                <span class="text-[10px] font-black uppercase text-zinc-600 md:hidden">Fatura:</span>
                                <span class="text-sm font-bold text-white">#{{ $fatura->id }}</span>
                            </div>
                            <div class="flex justify-between md:col-span-3 md:block md:px-3">
                                <span class="text-[10px] font-black uppercase text-zinc-600 md:hidden">Serviço:</span>
                                <span class="text-sm font-medium text-zinc-300">
                                    @if ($fatura->contrato)
                                        VPS {{ $fatura->contrato->vCPU }} (Contrato #{{ $fatura->contrato_id }})
                                    @else
                                        Serviço Indisponível
                                    @endif
                                </span>
                            </div>
                            <div class="flex justify-between md:col-span-2 md:block md:px-3">
                                <span class="text-[10px] font-black uppercase text-zinc-600 md:hidden">Vencimento:</span>
                                @if ($fatura->contrato && $fatura->contrato->data_proximo_vencimento)
                                    <span class="text-sm font-bold {{ $fatura->contrato->data_proximo_vencimento->isPast() ? 'text-red-500' : 'text-sky-400' }}">
                                        {{ $fatura->contrato->data_proximo_vencimento->format('d/m/Y') }}
                                    </span>
                                @else
                                     <span class="text-sm text-zinc-600">-</span>
                                @endif
                            </div>
                            <div class="flex justify-between md:col-span-2 md:block md:px-3">
                                <span class="text-[10px] font-black uppercase text-zinc-600 md:hidden">Valor:</span>
                                <span class="text-sm font-black text-white">R$ {{ number_format($fatura->valor, 2, ',', '.') }}</span>
                            </div>
                            <div class="pt-4 flex flex-col gap-2 md:col-span-3 md:flex-row md:pt-0 md:pr-8 md:justify-end">
                                <a href="{{ route('pagamento.exibir', ['contrato' => $fatura->contrato_id]) }}" class="py-2 px-4 rounded-xl bg-sky-400 text-black text-[10px] font-black uppercase tracking-widest text-center transition hover:bg-white">PIX</a>
                                <button type="button" @click="openModal({{ json_encode($fatura) }})" class="py-2 px-4 rounded-xl bg-white/5 border border-white/10 text-white text-[10px] font-black uppercase tracking-widest text-center transition hover:bg-white hover:text-black">Cartão</button>
                            </div>
                        </div>
                    @empty
                        <div class="py-16 text-center">
                            <p class="text-xs font-black uppercase tracking-widest text-zinc-600 italic">Nenhuma fatura pendente encontrada</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- HISTÓRICO -->
    @include('painel.faturas.partials.historico-pagamentos')

    <!-- MODAL DE PAGAMENTO -->
    <div x-show="modalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-6" style="display: none;" x-cloak>
        <div class="absolute inset-0 bg-black/80 backdrop-blur-sm" @click="closeModal()"></div>
        
        <div class="relative w-full max-w-lg fatura-card rounded-[2.5rem] overflow-hidden shadow-2xl border-sky-400/20" x-show="modalOpen" x-transition>
            <form id="paymentForm" :action="formAction" method="POST" @submit.prevent="submitPaymentForm">
                @csrf
                <input type="hidden" name="pagamento_id" :value="fatura ? fatura.id : ''">
                <input type="hidden" name="payment_token" id="payment_token">
                <input type="hidden" name="payment_method_id" id="payment_method_id">

                <div class="p-8 md:p-10">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="h-12 w-12 rounded-2xl bg-sky-400/10 flex items-center justify-center text-sky-400">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-white italic uppercase leading-none">Pagar com <span class="text-sky-400">Cartão</span></h3>
                            <p class="text-[10px] font-black text-zinc-500 uppercase tracking-widest mt-1">Fatura #<span x-text="fatura ? fatura.id : ''"></span></p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        @if($cards->isNotEmpty())
                            <p class="text-[10px] font-black uppercase text-zinc-600 tracking-widest ml-1">Selecione o Cartão</p>
                            <div class="grid grid-cols-1 gap-3">
                                @foreach($cards as $card)
                                <label class="selectable-card flex items-center justify-between bg-white/[0.03] p-4 rounded-2xl transition" :class="{'selected': selectedCardId == '{{ $card->id }}'}">
                                    <div class="flex items-center gap-4">
                                        <img src="{{ str_replace('http://', 'https://', $card->payment_method->thumbnail) }}" class="h-5">
                                        <span class="text-sm font-bold text-white italic">•••• {{ $card->last_four_digits }}</span>
                                    </div>
                                    <input type="radio" name="card_id" value="{{ $card->id }}" class="hidden-radio" x-model="selectedCardId" data-payment-method-id="{{ $card->payment_method->id }}">
                                </label>
                                @endforeach
                            </div>

                            <div class="mt-8">
                                <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Código de Segurança (CVV)</label>
                                <div class="bg-white/5 border border-white/10 rounded-2xl p-3">
                                    <div id="securityCode-container" style="height: 24px;"></div>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-6 bg-white/[0.02] border border-dashed border-white/10 rounded-2xl">
                                <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-4">Nenhum cartão vinculado</p>
                                <a href="{{ route('cartao.create') }}" class="inline-block py-2 px-6 rounded-xl bg-sky-400 text-black text-[10px] font-black uppercase tracking-widest">Vincular Agora</a>
                            </div>
                        @endif
                    </div>
                </div>

                @if($cards->isNotEmpty())
                <div class="p-8 bg-white/[0.02] border-t border-white/5 flex flex-col md:flex-row-reverse gap-3">
                    <button type="submit" :disabled="isProcessing" class="flex-1 py-4 rounded-2xl bg-sky-400 text-black text-xs font-black uppercase tracking-widest transition hover:bg-white disabled:opacity-50">
                        <span x-show="!isProcessing">Confirmar R$ <span x-text="fatura ? parseFloat(fatura.valor).toFixed(2).replace('.', ',') : '0,00'"></span></span>
                        <span x-show="isProcessing">Processando...</span>
                    </button>
                    <button type="button" @click="closeModal()" class="flex-1 py-4 rounded-2xl border border-white/10 text-white text-xs font-black uppercase tracking-widest hover:bg-white hover:text-black transition">
                        Cancelar
                    </button>
                </div>
                @endif
            </form>
        </div>
    </div>
</div>

<script src="https://sdk.mercadopago.com/js/v2"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('faturasPage', () => ({
            modalOpen: false,
            isProcessing: false,
            fatura: null,
            selectedCardId: '{{ $cards->first()->id ?? '' }}',
            formAction: '{{ route("painel.faturas.pagar.cartao") }}',
            mp: null,
            securityCodeElement: null,

            openModal(faturaData) {
                this.fatura = faturaData;
                this.modalOpen = true;
                this.$nextTick(() => { this.initMercadoPago(); });
            },

            closeModal() {
                this.modalOpen = false;
                if (this.securityCodeElement) { this.securityCodeElement.unmount(); this.securityCodeElement = null; }
            },

            initMercadoPago() {
                if (!this.mp) { this.mp = new MercadoPago("{{ config('services.mercadopago.public_key') }}"); }
                if (document.getElementById('securityCode-container')) {
                   this.securityCodeElement = this.mp.fields.create('securityCode', { style: { theme: 'dark' } }).mount('securityCode-container');
                }
            },

            async submitPaymentForm() {
                if (!this.selectedCardId) return;
                this.isProcessing = true;
                const paymentMethodInput = document.getElementById('payment_method_id');
                const tokenInput = document.getElementById('payment_token');
                const selectedCardRadio = document.querySelector(`input[name="card_id"][value="${this.selectedCardId}"]`);
                paymentMethodInput.value = selectedCardRadio.dataset.paymentMethodId;

                try {
                    const token = await this.mp.fields.createCardToken({ cardId: this.selectedCardId });
                    tokenInput.value = token.id;
                    document.getElementById('paymentForm').submit();
                } catch (e) {
                    alert('Falha ao validar CVV. Tente novamente.');
                    this.isProcessing = false;
                }
            }
        }));
    });
</script>
@endsection