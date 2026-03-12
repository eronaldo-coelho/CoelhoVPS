<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="manifest" href="/manifest.json">
    <meta name="theme-color" content="#0f172a">
    <title>CoelhoVPS — VPS Internacional com Suporte Brasileiro</title>

    <meta name="description" content="VPS Linux internacional com ativação automática, pagamento em Pix e suporte brasileiro. Ideal para bots, sites e sistemas.">

    <link rel="icon" href="{{ asset('logo.png?v=2') }}">

    @vite('resources/css/app.css')

    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root { --primary:#38bdf8; }

        body { background:#050505; }

        .bg-grid {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.04) 1px, transparent 0);
            background-size: 32px 32px;
        }

        .card {
            background: rgba(12,12,12,.88);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,.06);
            transition: .4s ease;
        }

        .card:hover {
            transform: translateY(-6px);
            border-color: var(--primary);
            box-shadow: 0 0 40px rgba(56,189,248,.12);
        }

        .discount-badge {
            position:absolute;
            top:-12px;
            right:28px;
            background:var(--primary);
            color:#000;
            padding:14px 16px;
            border-radius:0 0 14px 14px;
            text-align:center;
            font-weight:900;
        }
    </style>
</head>

<script>
if ("serviceWorker" in navigator) {
  navigator.serviceWorker.register("/sw.js");
}
</script>

<body class="text-zinc-400 antialiased overflow-x-hidden bg-grid">

<header class="fixed inset-x-0 top-0 z-50 bg-black/70 backdrop-blur-xl border-b border-white/5">
    <div class="container mx-auto px-6 h-28 flex items-center justify-between">
        <a href="/" class="flex items-center">
            <img 
                src="{{ asset('coelhovps.png') }}"
                alt="CoelhoVPS"
                class="h-16 sm:h-[72px] md:h-24 lg:h-28 w-auto transition-transform hover:scale-105"
            >
        </a>

        <nav class="hidden md:flex gap-10 text-sm font-semibold">
            <a href="/sobre-nos" class="hover:text-sky-400 transition">Sobre</a>
            <a href="/suporte" class="hover:text-sky-400 transition">Suporte</a>
        </nav>

        @auth
            <a href="{{ route('painel.dashboard') }}"
               class="px-6 py-2 rounded-full bg-white text-black text-xs font-black uppercase">
                Painel
            </a>
        @else
            <a href="{{ route('entrar.mostrar') }}"
               class="px-6 py-2 rounded-full border border-white/10 text-xs font-black uppercase hover:bg-white hover:text-black transition">
                Login
            </a>
        @endauth
    </div>
</header>

<main>

<section class="container mx-auto px-6 pt-40 pb-28 text-center">
    <div data-aos="fade-up">
        <span class="inline-block mb-6 px-4 py-1 text-[10px] font-black uppercase tracking-[0.3em]
                     border border-sky-400/30 rounded-full text-sky-400">
            Empresa Brasileira • Infra Global
        </span>

        <h1 class="text-5xl md:text-7xl font-black text-white leading-tight mb-6">
            VPS <span class="text-sky-400 italic">PODEROSA</span><br>
            SEM COMPLICAÇÃO
        </h1>

        <p class="max-w-2xl mx-auto text-zinc-500 text-lg">
            Servidores VPS Linux internacionais com ativação automática,
            pagamento em real via Pix e suporte humano em português.
        </p>

        <div class="mt-10 flex justify-center gap-6">
            <a href="#planos"
               class="px-10 py-4 rounded-2xl bg-sky-400 text-black font-black uppercase tracking-widest text-xs">
                Ver Planos
            </a>
            <a href="/suporte"
               class="px-10 py-4 rounded-2xl border border-white/10 font-black uppercase tracking-widest text-xs hover:bg-white hover:text-black transition">
                Falar com Suporte
            </a>
        </div>
    </div>

    <div class="mt-20 grid grid-cols-1 sm:grid-cols-3 gap-10 border-y border-white/5 py-10" data-aos="fade-up">
        <div>
            <p class="text-2xl font-black text-white">~5 min</p>
            <span class="text-[10px] uppercase tracking-widest text-zinc-600">Ativação</span>
        </div>
        <div>
            <p class="text-2xl font-black text-white">Pix / Real</p>
            <span class="text-[10px] uppercase tracking-widest text-zinc-600">Pagamento</span>
        </div>
        <div>
            <p class="text-2xl font-black text-white">24/7</p>
            <span class="text-[10px] uppercase tracking-widest text-zinc-600">Suporte BR</span>
        </div>
    </div>
</section>

<section class="container mx-auto px-6 py-24">
    <div class="grid md:grid-cols-4 gap-6">
        <div class="card p-8 rounded-3xl" data-aos="fade-up">
            <h3 class="text-white font-bold mb-2">Ativação Imediata</h3>
            <p class="text-sm text-zinc-500">Sua VPS pronta em minutos, sem aprovação manual.</p>
        </div>
        <div class="card p-8 rounded-3xl" data-aos="fade-up" data-aos-delay="100">
            <h3 class="text-white font-bold mb-2">Suporte Brasileiro</h3>
            <p class="text-sm text-zinc-500">Atendimento humano, rápido e em português.</p>
        </div>
        <div class="card p-8 rounded-3xl" data-aos="fade-up" data-aos-delay="200">
            <h3 class="text-white font-bold mb-2">Infra Global</h3>
            <p class="text-sm text-zinc-500">EUA, Europa e Ásia para menor latência.</p>
        </div>
        <div class="card p-8 rounded-3xl" data-aos="fade-up" data-aos-delay="300">
            <h3 class="text-white font-bold mb-2">Preço Justo</h3>
            <p class="text-sm text-zinc-500">Sem dólar, sem surpresas, sem fidelidade.</p>
        </div>
    </div>
</section>

<section id="planos" class="py-24 container mx-auto px-6">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-8 justify-items-center">
        @foreach($servidores as $servidor)
        @php
            $temDesconto = $servidor->desconto_percentual > 0;
            $valorFinal = $temDesconto
                ? $servidor->valor * (1 - $servidor->desconto_percentual / 100)
                : $servidor->valor;
        @endphp

        <div class="card p-10 rounded-[2.5rem] flex flex-col h-full relative w-full max-w-[380px]" data-aos="fade-up">
            @if($temDesconto)
                <div class="discount-badge">
                    <div class="text-xl leading-none">{{ $servidor->desconto_percentual }}%</div>
                    <div class="text-[9px] uppercase tracking-widest">OFF</div>
                </div>
            @endif

            <h3 class="text-3xl font-black text-white italic mb-6 text-center">
                VPS {{ $servidor->vCPU }}C
            </h3>

            <div class="mb-8 text-center">
                @if($temDesconto)
                    <span class="block text-sm line-through text-zinc-600">
                        R$ {{ number_format($servidor->valor, 2, ',', '.') }}
                    </span>
                @endif
                <span class="text-4xl font-black text-sky-400">
                    R$ {{ number_format($valorFinal, 2, ',', '.') }}
                </span>
                <span class="text-xs uppercase font-bold text-zinc-500">/mês</span>
            </div>

            <ul class="space-y-4 text-sm mb-10 flex-grow">
                <li class="flex items-center gap-3 border-b border-white/5 pb-2">
                    <span class="text-sky-400">⚡</span> {{ $servidor->vCPU }} vCPU
                </li>
                <li class="flex items-center gap-3 border-b border-white/5 pb-2">
                    <span class="text-sky-400">⚡</span> {{ $servidor->ram }} RAM
                </li>
                <li class="flex items-center gap-3 border-b border-white/5 pb-2">
                    <span class="text-sky-400">⚡</span> {{ $servidor->nvme }} 
                </li>
                <li class="flex items-center gap-3">
                    <span class="text-sky-400">⚡</span> {{ $servidor->snapshots }} Snapshot
                </li>
            </ul>

            <a href="{{ route('servidor.show', $servidor) }}"
               class="block w-full py-4 rounded-xl bg-sky-400 text-black font-black uppercase tracking-widest text-xs text-center transition hover:bg-white">
                Ativar Agora
            </a>
        </div>
        @endforeach
    </div>
</section>

</main>

<footer class="border-t border-white/5 py-16 text-center">
    <p class="text-[10px] uppercase tracking-[0.4em] text-zinc-600">
        © {{ date('Y') }} CoelhoVPS — Empresa Brasileira
    </p>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once:true, duration:800 });
</script>

</body>
</html>