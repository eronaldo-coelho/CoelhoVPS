<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pagamento PIX — CoelhoVPS</title>
    
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite('resources/css/app.css')
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <style>
        :root { --primary:#38bdf8; }
        body { background:#050505; }

        .bg-grid {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.04) 1px, transparent 0);
            background-size: 32px 32px;
        }

        .card-pix {
            background: rgba(12, 12, 12, 0.88);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .copy-area {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }

        .spinner-cyan {
            border-top-color: var(--primary);
            border-right-color: transparent;
            border-bottom-color: var(--primary);
            border-left-color: transparent;
        }
    </style>
</head>

<body class="text-zinc-400 antialiased bg-grid min-h-screen flex flex-col">

<header class="fixed inset-x-0 top-0 z-50 bg-black/70 backdrop-blur-xl border-b border-white/5">
    <div class="container mx-auto px-6 h-28 flex items-center justify-between">
        <a href="/" class="flex items-center">
            <img src="{{ asset('coelhovps.png') }}" alt="CoelhoVPS" class="h-16 sm:h-20 md:h-24 w-auto transition-transform hover:scale-105">
        </a>
        <div class="hidden md:flex items-center gap-4">
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500 italic">Pagamento Seguro via Pix</span>
            <div class="w-8 h-8 rounded-full bg-sky-400/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-sky-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15l-5-5 1.41-1.41L11 14.17l7.59-7.59L20 8l-9 9z"/></svg>
            </div>
        </div>
    </div>
</header>

<main class="flex-grow flex items-center justify-center pt-40 pb-20 px-6">
    <div class="w-full max-w-5xl">
        <div class="card-pix rounded-[3rem] p-8 md:p-16 shadow-2xl shadow-sky-400/5" data-aos="fade-up">
            
            <div class="grid lg:grid-cols-12 gap-12 items-center">
                
                <div class="lg:col-span-7 space-y-8">
                    <div>
                        <h1 class="text-4xl md:text-5xl font-black text-white italic tracking-tight uppercase">
                            Finalizar <span class="text-sky-400">Pagamento</span>
                        </h1>
                        <p class="text-zinc-500 font-bold uppercase tracking-widest text-xs mt-4">
                            Instância VPS — Contrato #{{ $pagamento->contrato_id }}
                        </p>
                    </div>

                    <div class="flex flex-col md:flex-row gap-8 items-center bg-white/[0.02] border border-white/5 p-8 rounded-[2rem]">
                        <div class="bg-white p-3 rounded-2xl shadow-2xl flex-shrink-0">
                            <img src="data:image/jpeg;base64,{{ $pagamento->qr_code_base64 }}" alt="QR Code PIX" class="w-48 h-48 md:w-56 md:h-56">
                        </div>
                        <div class="text-center md:text-left space-y-4">
                            <h2 class="text-xl font-black text-white italic uppercase tracking-tight">Escaneie o QR Code</h2>
                            <p class="text-sm text-zinc-500 leading-relaxed">
                                Utilize o aplicativo do seu banco para realizar o pagamento instantâneo e ativar seu servidor em minutos.
                            </p>
                            <div class="inline-flex items-center gap-2 px-4 py-2 bg-sky-400/10 rounded-full">
                                <span class="w-2 h-2 rounded-full bg-sky-400 animate-pulse"></span>
                                <span class="text-[10px] font-black text-sky-400 uppercase tracking-widest">Aguardando Confirmação</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 flex flex-col justify-center space-y-8">
                    <div class="p-8 rounded-[2.5rem] bg-sky-400 text-black shadow-xl shadow-sky-400/10">
                        <span class="text-[10px] font-black uppercase tracking-widest opacity-60">Valor do Investimento</span>
                        <div class="flex items-baseline gap-1 mt-2">
                            <span class="text-xl font-black">R$</span>
                            <span class="text-5xl font-black tracking-tighter">{{ number_format($pagamento->valor, 2, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="flex items-center justify-between ml-1">
                            <h2 class="text-[10px] font-black text-zinc-600 uppercase tracking-widest">Código Copia e Cola</h2>
                            <svg class="h-6 w-6 text-sky-400/50" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 48 48">
                                <path fill="currentColor" d="M11.9,12h-0.68l8.04-8.04c2.62-2.61,6.86-2.61,9.48,0L36.78,12H36.1c-1.6,0-3.11,0.62-4.24,1.76l-6.8,6.77c-0.59,0.59-1.53,0.59-2.12,0l-6.8-6.77C15.01,12.62,13.5,12,11.9,12z"></path>
                                <path fill="currentColor" d="M36.1,36h0.68l-8.04,8.04c-2.62,2.61-6.86,2.61-9.48,0L11.22,36h0.68c1.6,0,3.11-0.62,4.24-1.76l6.8-6.77c0.59-0.59,1.53-0.59,2.12,0l6.8,6.77C32.99,35.38,34.5,36,36.1,36z"></path>
                                <path fill="currentColor" d="M44.04,28.74L38.78,34H36.1c-1.07,0-2.07-0.42-2.83-1.17l-6.8-6.78c-1.36-1.36-3.58-1.36-4.94,0l-6.8,6.78C13.97,33.58,12.97,34,11.9,34H9.22l-5.26-5.26c-2.61-2.62-2.61-6.86,0-9.48L9.22,14h2.68c1.07,0,2.07,0.42,2.83,1.17l6.8,6.78c0.68,0.68,1.58,1.02,2.47,1.02s1.79-0.34,2.47-1.02l6.8-6.78C34.03,14.42,35.03,14,36.1,14h2.68l5.26,5.26C46.65,21.88,46.65,26.12,44.04,28.74z"></path>
                            </svg>
                        </div>
                        <div class="relative group">
                            <textarea id="pix-copia-cola" rows="3" readonly class="copy-area block w-full p-4 pr-14 text-sm text-zinc-400 rounded-2xl resize-none focus:outline-none focus:border-sky-400/50 transition-all select-all">{{ $pagamento->qr_code_text }}</textarea>
                            <button id="copy-button" class="absolute top-1/2 -translate-y-1/2 right-4 p-3 bg-white/5 hover:bg-sky-400 hover:text-black rounded-xl transition-all duration-300">
                                <svg id="copy-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                <svg id="check-icon" class="w-5 h-5 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </button>
                        </div>
                        <p class="text-[10px] text-zinc-600 font-bold uppercase tracking-tight text-center">
                            Expira em: {{ $pagamento->data_vencimento->format('d/m/Y \à\s H:i') }}
                        </p>
                    </div>

                    <div class="pt-6 border-t border-white/5 flex items-center justify-center gap-4">
                        <div class="animate-spin h-5 w-5 border-4 border-zinc-800 spinner-cyan rounded-full"></div>
                        <span class="text-xs font-black uppercase tracking-widest text-zinc-500">Aguardando Recebimento</span>
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-12 text-center" data-aos="fade-up" data-aos-delay="200">
            <a href="{{ route('painel.dashboard') }}" class="text-xs font-black uppercase tracking-[0.3em] text-zinc-600 hover:text-sky-400 transition">
                ← Voltar para o Painel de Controle
            </a>
        </div>
    </div>
</main>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once: true, duration: 800 });

    const btn = document.getElementById('copy-button');
    const input = document.getElementById('pix-copia-cola');
    const icon = document.getElementById('copy-icon');
    const check = document.getElementById('check-icon');

    btn.addEventListener('click', () => {
        navigator.clipboard.writeText(input.value).then(() => {
            icon.classList.add('hidden');
            check.classList.remove('hidden');
            setTimeout(() => {
                icon.classList.remove('hidden');
                check.classList.add('hidden');
            }, 2500);
        });
    });
</script>

</body>
</html>