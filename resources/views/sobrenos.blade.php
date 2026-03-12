<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nós — CoelhoVPS</title>
    
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

        .card {
            background: rgba(12,12,12,.88);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255,255,255,.06);
        }

        .content-section h2 {
            font-size: 1.875rem;
            font-weight: 900;
            color: #fff;
            font-style: italic;
            margin-top: 3rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .content-section h2::before {
            content: "";
            width: 4px;
            height: 24px;
            background: var(--primary);
            border-radius: 2px;
        }

        .content-section p {
            color: #a1a1aa;
            line-height: 1.8;
            margin-bottom: 1.5rem;
            font-size: 1.05rem;
        }

        .feature-icon {
            flex-shrink: 0;
            width: 3rem;
            height: 3rem;
            background: rgba(56,189,248, 0.1);
            border: 1px solid rgba(56,189,248, 0.2);
            color: var(--primary);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1.25rem;
        }

        .stat-card {
            padding: 2rem;
            text-align: center;
            border-bottom: 2px solid transparent;
            transition: 0.3s;
        }

        .stat-card:hover {
            border-bottom-color: var(--primary);
            background: rgba(255,255,255,0.02);
        }
    </style>
</head>

<body class="text-zinc-400 antialiased bg-grid min-h-screen">

<header class="fixed inset-x-0 top-0 z-50 bg-black/70 backdrop-blur-xl border-b border-white/5">
    <div class="container mx-auto px-6 h-28 flex items-center justify-between">
        <a href="/" class="flex items-center">
            <img src="{{ asset('coelhovps.png') }}" alt="CoelhoVPS" class="h-16 sm:h-20 md:h-24 w-auto transition-transform hover:scale-105">
        </a>

        <nav class="hidden md:flex gap-10 text-sm font-semibold">
            <a href="{{ route('sobrenos.mostrar') }}" class="text-sky-400">Sobre</a>
            <a href="{{ route('suporte.mostrar') }}" class="hover:text-sky-400 transition">Suporte</a>
        </nav>

        <a href="{{ route('entrar.mostrar') }}" class="px-6 py-2 rounded-full border border-white/10 text-xs font-black uppercase hover:bg-white hover:text-black transition">
            Acessar Painel
        </a>
    </div>
</header>

<main class="pt-48 pb-24">
    <div class="container mx-auto px-6">
        
        <div class="text-center mb-24" data-aos="fade-up">
            <span class="inline-block mb-4 px-4 py-1 text-[10px] font-black uppercase tracking-[0.3em] border border-sky-400/30 rounded-full text-sky-400">
                Infraestrutura de Elite
            </span>
            <h1 class="text-5xl md:text-7xl font-black text-white leading-tight mb-6">
                POTÊNCIA GLOBAL,<br><span class="text-sky-400 italic">CORAÇÃO BRASILEIRO.</span>
            </h1>
            <p class="max-w-3xl mx-auto text-zinc-500 text-lg md:text-xl">
                Nascemos para eliminar as barreiras entre você e a melhor tecnologia do mundo. 
                Servidores internacionais com a facilidade de pagamento e suporte que só um brasileiro entende.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-24" data-aos="fade-up" data-aos-delay="100">
            <div class="card stat-card rounded-3xl">
                <div class="text-3xl font-black text-white mb-1">99.9%</div>
                <div class="text-[10px] uppercase tracking-widest font-bold text-sky-400">Uptime Garantido</div>
            </div>
            <div class="card stat-card rounded-3xl">
                <div class="text-3xl font-black text-white mb-1">~5 min</div>
                <div class="text-[10px] uppercase tracking-widest font-bold text-sky-400">Setup Automático</div>
            </div>
            <div class="card stat-card rounded-3xl">
                <div class="text-3xl font-black text-white mb-1">24/7</div>
                <div class="text-[10px] uppercase tracking-widest font-bold text-sky-400">Suporte em PT-BR</div>
            </div>
            <div class="card stat-card rounded-3xl">
                <div class="text-3xl font-black text-white mb-1">Gen 4</div>
                <div class="text-[10px] uppercase tracking-widest font-bold text-sky-400">NVMe de Ultra Velocidade</div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto card p-8 md:p-16 rounded-[3rem] content-section" data-aos="fade-up">
            
            <section>
                <h2>Nossa Missão</h2>
                <p>Na <strong>CoelhoVPS</strong>, não apenas vendemos servidores; entregamos a base para o seu sucesso. democratizamos o acesso à computação de alta performance, removendo as complicações de IO, latência e pagamentos burocráticos em dólar.</p>
                <p>Acreditamos que o desenvolvedor brasileiro merece o mesmo poder computacional de uma startup do Vale do Silício, com a praticidade de pagar via Pix e ser atendido em sua própria língua.</p>
            </section>

            <section class="mt-16">
                <h2>Transparência e Confiança</h2>
                <p>Sua tranquilidade é nossa prioridade. Para reforçar nosso compromisso com a qualidade e o respeito ao cliente, a CoelhoVPS está oficialmente cadastrada no <strong>Reclame Aqui</strong>. Isso garante que você tenha um canal direto e público para validar nossa seriedade.</p>
                <a href="https://www.reclameaqui.com.br/empresa/coelhovps/" target="_blank" class="inline-flex items-center gap-4 px-8 py-4 rounded-2xl border border-white/10 bg-white/5 hover:bg-white/10 hover:border-sky-400/50 transition group">
                    <img src="https://vtlogo.com/wp-content/uploads/2020/10/reclame-aqui-vector-logo.png" alt="Reclame Aqui" class="h-6">
                    <span class="text-white font-bold text-sm tracking-tight">Verificar reputação no Reclame Aqui</span>
                    <svg class="w-4 h-4 text-sky-400 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                </a>
            </section>

            <section class="mt-16">
                <h2>O Padrão CoelhoVPS</h2>
                <div class="grid grid-cols-1 gap-8 mt-10">
                    <div class="flex items-start">
                        <div class="feature-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-white font-black italic uppercase tracking-tight mb-2">Hardware Sem Concessões</h4>
                            <p class="text-sm">Operamos exclusivamente com CPUs AMD de alta frequência e armazenamento NVMe Gen 4. Isso significa que sua aplicação não apenas "roda", ela voa.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="feature-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-white font-black italic uppercase tracking-tight mb-2">Segurança em Camadas</h4>
                            <p class="text-sm">Proteção Anti-DDoS profissional está inclusa em cada IP. Seus dados e sua operação ficam blindados contra ataques de negação de serviço 24 horas por dia.</p>
                        </div>
                    </div>

                    <div class="flex items-start">
                        <div class="feature-icon">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"/></svg>
                        </div>
                        <div>
                            <h4 class="text-white font-black italic uppercase tracking-tight mb-2">Conectividade Global</h4>
                            <p class="text-sm">Nossos pontos de presença nos EUA, Europa e Ásia garantem que sua audiência global tenha a menor latência possível, onde quer que estejam.</p>
                        </div>
                    </div>
                </div>
            </section>

            <div class="mt-20 pt-12 border-t border-white/5 text-center">
                <h3 class="text-2xl font-black text-white italic mb-6">PRONTO PARA EVOLUIR SUA INFRA?</h3>
                <a href="/#planos" class="inline-block px-12 py-5 rounded-2xl bg-sky-400 text-black font-black uppercase tracking-[0.2em] text-xs transition transform hover:scale-105 hover:bg-white active:scale-95">
                    Ver Planos Disponíveis
                </a>
            </div>

        </div>
    </div>
</main>

<footer class="border-t border-white/5 py-16 text-center">
    <div class="container mx-auto px-6">
        <img src="{{ asset('coelhovps.png') }}" alt="CoelhoVPS" class="h-12 w-auto mx-auto mb-8 opacity-50 grayscale">
        <p class="text-[10px] uppercase tracking-[0.4em] text-zinc-600">
            © {{ date('Y') }} CoelhoVPS — Empresa Brasileira Atuando Globalmente
        </p>
    </div>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once:true, duration:800 });
</script>

</body>
</html>