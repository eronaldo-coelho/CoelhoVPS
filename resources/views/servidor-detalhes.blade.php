<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar VPS {{ $servidor->vCPU }} Core - CoelhoVPS</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite('resources/css/app.css')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root { --primary:#38bdf8; }
        body { background:#050505; }
        .bg-grid { background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.04) 1px, transparent 0); background-size: 32px 32px; }
        .card { background: rgba(12,12,12,.88); backdrop-filter: blur(14px); border: 1px solid rgba(255,255,255,.06); transition: .3s ease; }
        .selectable-card { cursor: pointer; border: 2px solid rgba(255,255,255,.05); transition: all 0.3s ease; }
        .selectable-card:hover { border-color: rgba(56,189,248, 0.4); transform: translateY(-2px); }
        .selected { border-color: var(--primary) !important; background: rgba(56,189,248, 0.03); box-shadow: 0 0 25px rgba(56,189,248, 0.1); }
        .hidden-radio { display: none; }
        .check-indicator { width: 20px; height: 20px; border-radius: 50%; border: 2px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; transition: 0.3s; }
        .selected .check-indicator { background: var(--primary); border-color: var(--primary); }
        .os-tab { cursor: pointer; padding: 12px 20px; border-radius: 12px; background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); font-weight: 700; transition: 0.3s; text-align: center; }
        .os-tab.active { background: var(--primary); color: #000; }
    </style>
</head>

<body class="text-zinc-400 antialiased bg-grid min-h-screen">

@php
    $valorComDesconto = $servidor->valor * (1 - ($servidor->desconto_percentual / 100));
@endphp

<header class="fixed inset-x-0 top-0 z-50 bg-black/70 backdrop-blur-xl border-b border-white/5">
    <div class="container mx-auto px-6 h-28 flex items-center justify-between">
        <a href="/" class="flex items-center">
            <img src="{{ asset('coelhovps.png') }}" alt="CoelhoVPS" class="h-16 sm:h-20 md:h-24 w-auto transition-transform hover:scale-105">
        </a>
        <nav class="hidden md:flex gap-10 text-sm font-semibold">
            <a href="/sobre-nos" class="hover:text-sky-400 transition">Sobre</a>
            <a href="/suporte" class="hover:text-sky-400 transition">Suporte</a>
        </nav>
        @auth
            <a href="{{ route('painel.dashboard') }}" class="px-6 py-2 rounded-full bg-white text-black text-xs font-black uppercase">Painel</a>
        @else
            <a href="{{ route('entrar.mostrar') }}" class="px-6 py-2 rounded-full border border-white/10 text-xs font-black uppercase hover:bg-white hover:text-black transition">Login</a>
        @endauth
    </div>
</header>

<main class="pt-40 pb-24">
    <div class="container mx-auto px-6">
        <div class="mb-16" data-aos="fade-up">
            <span class="inline-block mb-4 px-4 py-1 text-[10px] font-black uppercase tracking-[0.3em] border border-sky-400/30 rounded-full text-sky-400">Configuração de Instância</span>
            <h1 class="text-4xl md:text-5xl font-black text-white italic">VPS {{ $servidor->vCPU }} <span class="text-sky-400">CORE</span></h1>
        </div>

        <form action="{{ route('checkout.iniciar') }}" method="POST" class="grid grid-cols-1 lg:grid-cols-12 gap-12">
            @csrf
            <input type="hidden" name="servidor_id" value="{{ $servidor->id }}">

            <div class="lg:col-span-8 space-y-16">
                <section data-aos="fade-up">
                    <div class="flex items-center gap-4 mb-8">
                        <span class="w-8 h-8 rounded-lg bg-sky-400 text-black flex items-center justify-center font-black">1</span>
                        <h2 class="text-2xl font-black text-white uppercase tracking-tight">Armazenamento</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($opcoesDisco as $index => $opcao)
                            @php 
                                $isChecked = isset($configuracaoSalva['product_id']) ? ($configuracaoSalva['product_id'] == $opcao->product_id) : ($index == 0);
                            @endphp
                            <label class="selectable-card card p-6 rounded-2xl flex items-center justify-between {{ $isChecked ? 'selected' : '' }}">
                                <div>
                                    <p class="text-xs font-bold text-zinc-500 uppercase tracking-widest mb-1">{{ $opcao->product }}</p>
                                    <h3 class="text-2xl font-black text-white">{{ $opcao->disk_size }}</h3>
                                </div>
                                <div class="check-indicator">
                                    <svg class="w-3 h-3 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <input type="radio" name="product_id" value="{{ $opcao->product_id }}" class="hidden-radio" {{ $isChecked ? 'checked' : '' }}>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section data-aos="fade-up" data-aos-delay="100">
                    <div class="flex items-center gap-4 mb-8">
                        <span class="w-8 h-8 rounded-lg bg-sky-400 text-black flex items-center justify-center font-black">2</span>
                        <h2 class="text-2xl font-black text-white uppercase tracking-tight">Localização</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach ($regioes as $index => $regiao)
                            @php 
                                $custoAdicional = $valorComDesconto * ($regiao->porcentagem / 100); 
                                $isRegiaoChecked = isset($configuracaoSalva['regiao_id']) ? ($configuracaoSalva['regiao_id'] == $regiao->regiao_id) : ($index == 0);
                            @endphp
                            <label class="selectable-card card p-6 rounded-2xl flex items-center justify-between {{ $isRegiaoChecked ? 'selected' : '' }}">
                                <div>
                                    <h3 class="text-lg font-bold text-white">{{ $regiao->regiao }}</h3>
                                    <p class="text-sm {{ $custoAdicional > 0 ? 'text-sky-400' : 'text-zinc-500' }}">
                                        {{ $regiao->latencia }}ms • {{ $custoAdicional > 0 ? '+ R$ '.number_format($custoAdicional, 2, ',', '.') : 'Incluso' }}
                                    </p>
                                </div>
                                <div class="check-indicator">
                                    <svg class="w-3 h-3 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <input type="radio" name="regiao_id" value="{{ $regiao->regiao_id }}" class="hidden-radio" data-percentage="{{ $regiao->porcentagem }}" {{ $isRegiaoChecked ? 'checked' : '' }}>
                            </label>
                        @endforeach
                    </div>
                </section>

                <section data-aos="fade-up" data-aos-delay="200">
                    <div class="flex items-center gap-4 mb-8">
                        <span class="w-8 h-8 rounded-lg bg-sky-400 text-black flex items-center justify-center font-black">3</span>
                        <h2 class="text-2xl font-black text-white uppercase tracking-tight">Sistema Operacional</h2>
                    </div>
                    <div class="flex flex-wrap gap-3 mb-8">
                        @foreach ($sistemasAgrupados as $familia => $versoes)
                            @php $hasSelectedInFamily = $versoes->contains('image_id', $configuracaoSalva['sistema_id'] ?? null); @endphp
                            <div class="os-tab flex-1 min-w-[120px] {{ $hasSelectedInFamily ? 'active' : '' }}" data-target="versions-{{ Str::slug($familia) }}">
                                {{ $familia }}
                            </div>
                        @endforeach
                    </div>
                    @foreach ($sistemasAgrupados as $familia => $versoes)
                        <div id="versions-{{ Str::slug($familia) }}" class="os-content {{ $versoes->contains('image_id', $configuracaoSalva['sistema_id'] ?? null) ? '' : 'hidden' }}">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @foreach ($versoes as $versao)
                                    @php $isOsChecked = ($configuracaoSalva['sistema_id'] ?? null) == $versao->image_id; @endphp
                                    <label class="selectable-card card p-5 rounded-2xl flex items-center justify-between {{ $isOsChecked ? 'selected' : '' }}">
                                        <span class="text-white font-medium">{{ $versao->description }}</span>
                                        <div class="check-indicator">
                                            <svg class="w-3 h-3 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                        <input type="radio" name="sistema_id" value="{{ $versao->image_id }}" class="hidden-radio" {{ $isOsChecked ? 'checked' : '' }}>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </section>
            </div>

            <aside class="lg:col-span-4">
                <div class="sticky top-32 card p-8 rounded-[2.5rem] border-sky-400/20 shadow-2xl shadow-sky-400/5">
                    <h2 class="text-2xl font-black text-white italic mb-8">Resumo</h2>
                    <ul class="space-y-4 mb-8 text-sm">
                        <li class="flex justify-between border-b border-white/5 pb-2"><span class="text-zinc-500">vCPU</span><span class="text-white font-bold">{{ $servidor->vCPU }} Cores</span></li>
                        <li class="flex justify-between border-b border-white/5 pb-2"><span class="text-zinc-500">Memória</span><span class="text-white font-bold">{{ $servidor->ram }}</span></li>
                        <li class="flex justify-between border-b border-white/5 pb-2"><span class="text-zinc-500">Tráfego</span><span class="text-white font-bold">{{ $servidor->traffic }}</span></li>
                    </ul>
                    <div class="space-y-2 mb-10">
                        <div class="flex justify-between text-xs uppercase tracking-widest font-bold text-zinc-600">
                            <span>Plano Base</span>
                            <span id="base-price" data-price="{{ $valorComDesconto }}">R$ {{ number_format($valorComDesconto, 2, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between text-xs uppercase tracking-widest font-bold text-zinc-600">
                            <span>Adicionais</span>
                            <span id="additional-cost">R$ 0,00</span>
                        </div>
                        <div class="pt-4 flex justify-between items-end">
                            <span class="text-white font-black italic">TOTAL</span>
                            <div class="text-right">
                                <span id="total-cost" class="text-4xl font-black text-sky-400 block leading-none">R$ 0,00</span>
                                <span class="text-[10px] text-zinc-500 uppercase font-bold tracking-tighter">Cobrança Mensal</span>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="w-full py-5 rounded-2xl bg-sky-400 text-black font-black uppercase tracking-[0.2em] text-xs transition transform hover:scale-[1.02] hover:bg-white active:scale-95">Finalizar Pedido</button>
                    <div class="mt-6 flex items-center justify-center gap-4 opacity-40">
                        <img src="https://logodownload.org/wp-content/uploads/2020/02/pix-bc-logo.png" class="h-4 grayscale invert" alt="Pix">
                        <div class="h-4 w-px bg-white/20"></div>
                        <span class="text-[9px] font-black uppercase tracking-widest text-white">Ativação Instantânea</span>
                    </div>
                </div>
            </aside>
        </form>
    </div>
</main>

<footer class="border-t border-white/5 py-16 text-center">
    <p class="text-[10px] uppercase tracking-[0.4em] text-zinc-600">© {{ date('Y') }} CoelhoVPS — Tecnologia para Elite</p>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once:true, duration:800 });
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('form');
        function refreshStyles() {
            document.querySelectorAll('.selectable-card').forEach(card => {
                const radio = card.querySelector('input');
                if (radio.checked) card.classList.add('selected');
                else card.classList.remove('selected');
            });
        }
        function calculateTotal() {
            const basePrice = parseFloat(document.getElementById('base-price').dataset.price);
            const selectedRegion = document.querySelector('input[name="regiao_id"]:checked');
            const percentage = parseFloat(selectedRegion ? selectedRegion.dataset.percentage : 0);
            const additional = basePrice * (percentage / 100);
            const total = basePrice + additional;
            const fmt = (v) => `R$ ${v.toFixed(2).replace('.', ',')}`;
            document.getElementById('additional-cost').textContent = fmt(additional);
            document.getElementById('total-cost').textContent = fmt(total);
        }
        form.addEventListener('change', () => { refreshStyles(); calculateTotal(); });
        const osTabs = document.querySelectorAll('.os-tab');
        const osContents = document.querySelectorAll('.os-content');
        osTabs.forEach(tab => {
            tab.addEventListener('click', () => {
                osTabs.forEach(t => t.classList.remove('active'));
                tab.classList.add('active');
                const target = tab.dataset.target;
                osContents.forEach(c => {
                    if(c.id === target) {
                        c.classList.remove('hidden');
                        if(!c.querySelector('input:checked')) {
                            const firstRadio = c.querySelector('input');
                            if(firstRadio) firstRadio.checked = true;
                        }
                    } else { c.classList.add('hidden'); }
                });
                refreshStyles();
            });
        });
        const activeTab = document.querySelector('.os-tab.active') || osTabs[0];
        if(activeTab) activeTab.click();
        refreshStyles();
        calculateTotal();
    });
</script>
</body>
</html>