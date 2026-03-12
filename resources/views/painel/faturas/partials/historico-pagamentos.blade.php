<div class="mt-20" data-aos="fade-up" data-aos-delay="400">
    <div class="mb-6 flex items-center gap-3">
        <h2 class="text-lg font-black text-white italic uppercase">Histórico de <span class="text-sky-400">Pagamentos</span></h2>
    </div>

    <div class="overflow-hidden rounded-[2rem] fatura-card shadow-xl">
        <div class="min-w-full">
            <div class="hidden bg-white/5 md:grid md:grid-cols-12 md:gap-4 border-b border-white/5 text-center">
                <div class="py-4 pl-8 text-left text-[10px] font-black uppercase tracking-widest text-zinc-500 md:col-span-2">ID #</div>
                <div class="py-4 px-3 text-left text-[10px] font-black uppercase tracking-widest text-zinc-500 md:col-span-3">Serviço</div>
                <div class="py-4 px-3 text-[10px] font-black uppercase tracking-widest text-zinc-500 md:col-span-2">Data Pag.</div>
                <div class="py-4 px-3 text-[10px] font-black uppercase tracking-widest text-zinc-500 md:col-span-2">Método</div>
                <div class="py-4 px-3 text-[10px] font-black uppercase tracking-widest text-zinc-500 md:col-span-2">Valor</div>
                <div class="py-4 pr-8 text-[10px] font-black uppercase tracking-widest text-zinc-500 md:col-span-1">Status</div>
            </div>

            <div class="divide-y divide-white/5">
                @forelse ($faturasPagas as $fatura)
                    <div class="grid grid-cols-1 gap-y-3 p-6 transition-colors hover:bg-white/[0.02] md:grid-cols-12 md:gap-4 md:p-0 md:items-center text-center">
                        <div class="flex justify-between md:col-span-2 md:block md:py-6 md:pl-8 md:text-left">
                            <span class="text-[10px] font-black uppercase text-zinc-600 md:hidden">Fatura:</span>
                            <span class="text-sm font-bold text-white">#{{ $fatura->id }}</span>
                        </div>
                        <div class="flex justify-between md:col-span-3 md:block md:px-3 md:text-left">
                            <span class="text-[10px] font-black uppercase text-zinc-600 md:hidden">Serviço:</span>
                            <span class="text-sm font-medium text-zinc-300">VPS {{ $fatura->contrato->vCPU ?? 'Extra' }}</span>
                        </div>
                        <div class="flex justify-between md:col-span-2 md:block md:px-3">
                            <span class="text-[10px] font-black uppercase text-zinc-600 md:hidden">Pagamento:</span>
                            <span class="text-sm text-zinc-500">{{ $fatura->data_pagamento ? $fatura->data_pagamento->format('d/m/Y') : '-' }}</span>
                        </div>
                        <div class="flex justify-between md:col-span-2 md:block md:px-3">
                            <span class="text-[10px] font-black uppercase text-zinc-600 md:hidden">Método:</span>
                            <span>
                                @if($fatura->isPix())
                                    <span class="px-2 py-0.5 rounded-full bg-sky-400/10 text-sky-400 text-[10px] font-black uppercase">PIX</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full bg-white/5 text-zinc-400 text-[10px] font-black uppercase italic">Cartão</span>
                                @endif
                            </span>
                        </div>
                        <div class="flex justify-between md:col-span-2 md:block md:px-3">
                            <span class="text-[10px] font-black uppercase text-zinc-600 md:hidden">Valor:</span>
                            <span class="text-sm font-black text-white">R$ {{ number_format($fatura->valor, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between md:col-span-1 md:block md:pr-8">
                            <span class="text-[10px] font-black uppercase text-zinc-600 md:hidden">Status:</span>
                            <span class="text-sky-400">
                                <svg class="w-5 h-5 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            </span>
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center italic text-zinc-600 text-xs font-bold uppercase tracking-widest">
                        Histórico de pagamentos vazio
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>