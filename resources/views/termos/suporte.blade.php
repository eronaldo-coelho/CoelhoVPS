<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suporte e FAQ — CoelhoVPS</title>
    
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
            transition: .3s ease;
        }

        .faq-item {
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }

        .faq-question {
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1.5rem 0;
            transition: 0.3s;
        }

        .faq-question:hover {
            color: var(--primary);
        }

        .faq-answer {
            max-height: 0;
            overflow: hidden;
            transition: all 0.5s cubic-bezier(0, 1, 0, 1);
            color: #71717a;
            font-size: 0.95rem;
            line-height: 1.6;
        }

        .faq-item.active .faq-answer {
            max-height: 1000px;
            padding-bottom: 1.5rem;
            transition: all 0.5s cubic-bezier(1, 0, 1, 0);
        }

        .faq-icon {
            transition: transform 0.3s ease;
        }

        .faq-item.active .faq-icon {
            transform: rotate(45deg);
            color: var(--primary);
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
            <a href="/sobre-nos" class="hover:text-sky-400 transition">Sobre</a>
            <a href="{{ route('suporte.mostrar') }}" class="text-sky-400">Suporte</a>
        </nav>

        @auth
            <a href="{{ route('painel.dashboard') }}" class="px-6 py-2 rounded-full bg-white text-black text-xs font-black uppercase">Painel</a>
        @else
            <a href="{{ route('entrar.mostrar') }}" class="px-6 py-2 rounded-full border border-white/10 text-xs font-black uppercase hover:bg-white hover:text-black transition">Login</a>
        @endauth
    </div>
</header>

<main class="pt-48 pb-24">
    <div class="container mx-auto px-6">
        
        <div class="text-center mb-24" data-aos="fade-up">
            <span class="inline-block mb-4 px-4 py-1 text-[10px] font-black uppercase tracking-[0.3em] border border-sky-400/30 rounded-full text-sky-400">
                Central de Atendimento
            </span>
            <h1 class="text-5xl md:text-7xl font-black text-white leading-tight mb-6 italic">
                COMO PODEMOS <span class="text-sky-400">AJUDAR?</span>
            </h1>
            <p class="max-w-2xl mx-auto text-zinc-500 text-lg">
                Nossa equipe de especialistas está pronta para garantir que sua operação nunca pare. 
                Escolha o canal de sua preferência abaixo.
            </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto mb-24">
            <div class="card p-10 rounded-[2.5rem] flex flex-col items-center text-center group hover:border-sky-400/40 transition" data-aos="fade-right">
                <div class="w-16 h-16 bg-sky-400/10 rounded-2xl flex items-center justify-center mb-6 text-sky-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-white italic mb-2">E-mail Oficial</h3>
                <p class="text-sm text-zinc-500 mb-6">Para questões administrativas ou técnicas complexas.</p>
                <a href="mailto:comercial@coelhovps.com.br" class="text-sky-400 font-bold hover:underline">comercial@coelhovps.com.br</a>
            </div>

            <div class="card p-10 rounded-[2.5rem] flex flex-col items-center text-center group border-sky-400/20 bg-sky-400/[0.02]" data-aos="fade-left">
                <div class="w-16 h-16 bg-sky-400 rounded-2xl flex items-center justify-center mb-6 text-black">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L0 24l6.335-1.662c1.72.937 3.672 1.433 5.66 1.433h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                </div>
                <h3 class="text-2xl font-black text-white italic mb-2">WhatsApp VIP</h3>
                <p class="text-sm text-zinc-500 mb-6">Canal recomendado para suporte ágil e imediato.</p>
                <a href="https://wa.me/5513982038196" target="_blank" class="text-sky-400 font-bold text-xl">+55 13 98203-8196</a>
            </div>
        </div>

        <section class="max-w-4xl mx-auto mb-24" data-aos="fade-up">
            <div class="card p-10 rounded-[3rem] border-sky-400/20 bg-sky-400/[0.01]">
                <div class="flex items-center gap-4 mb-6">
                    <div class="p-3 bg-sky-400/10 rounded-xl text-sky-400">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                    </div>
                    <h2 class="text-2xl font-black text-white italic tracking-tight uppercase">Garantia e Reembolso</h2>
                </div>
                <div class="space-y-4 text-zinc-400 leading-relaxed">
                    <p>Prezamos pela transparência e satisfação total de nossos clientes. Caso o serviço contratado não atenda às suas expectativas técnicas, a <strong>CoelhoVPS</strong> oferece uma política de reembolso específica:</p>
                    
                    <ul class="grid grid-cols-1 md:grid-cols-2 gap-4 my-6">
                        <li class="flex items-center gap-3 text-sm font-bold text-white bg-white/5 p-4 rounded-xl">
                            <span class="text-sky-400">✓</span> Prazo de até 10 dias corridos
                        </li>
                        <li class="flex items-center gap-3 text-sm font-bold text-white bg-white/5 p-4 rounded-xl">
                            <span class="text-sky-400">✓</span> Válido apenas na 1ª contratação
                        </li>
                        <li class="flex items-center gap-3 text-sm font-bold text-white bg-white/5 p-4 rounded-xl">
                            <span class="text-sky-400">✓</span> Reembolso proporcional ao uso
                        </li>
                        <li class="flex items-center gap-3 text-sm font-bold text-white bg-white/5 p-4 rounded-xl">
                            <span class="text-sky-400">✓</span> Atendimento via WhatsApp/E-mail
                        </li>
                    </ul>

                    <p class="text-sm">O montante a ser restituído será calculado de forma proporcional, subtraindo-se os dias em que o servidor esteve provisionado e ativo. Para solicitar o estorno, o titular deve entrar em contato com nossa equipe de atendimento em até <strong>10 dias após o pagamento inicial</strong>. Recomendamos o uso do <strong>WhatsApp</strong> para maior celeridade no processo.</p>
                </div>
            </div>
        </section>

        <section class="max-w-4xl mx-auto" data-aos="fade-up">
            <h2 class="text-3xl font-black text-white text-center italic mb-12 uppercase tracking-tight">Perguntas <span class="text-sky-400">Frequentes</span></h2>
            
            <div class="space-y-2">
                <div class="faq-item">
                    <div class="faq-question">
                        <span class="font-bold text-white italic">Qual o prazo de entrega do meu servidor?</span>
                        <span class="faq-icon text-xl">+</span>
                    </div>
                    <div class="faq-answer">
                        O provisionamento é totalmente automatizado. Após a confirmação do pagamento pelo gateway, sua VPS é ativada em aproximadamente 5 minutos. Os dados de acesso (IP e Senha Root) são enviados imediatamente para seu e-mail cadastrado.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span class="font-bold text-white italic">Tenho acesso Root total ao servidor?</span>
                        <span class="faq-icon text-xl">+</span>
                    </div>
                    <div class="faq-answer">
                        Sim. Você possui controle administrativo total (Root via SSH) sobre sua instância Linux. Isso permite que você instale qualquer aplicação, banco de dados ou script que desejar, sem restrições de software.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span class="font-bold text-white italic">Como funciona a proteção DDoS?</span>
                        <span class="faq-icon text-xl">+</span>
                    </div>
                    <div class="faq-answer">
                        Nossa proteção DDoS opera em nível de rede (Always-On). Ela filtra o tráfego malicioso em tempo real antes mesmo de atingir sua VPS, mitigando ataques de inundação e garantindo que seu serviço permaneça online durante tentativas de queda.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span class="font-bold text-white italic">Posso realizar upgrade de plano futuramente?</span>
                        <span class="faq-icon text-xl">+</span>
                    </div>
                    <div class="faq-answer">
                        Sim, você pode escalar seus recursos conforme seu projeto cresce. O upgrade pode ser solicitado via ticket em nosso painel, e o processo preserva todos os dados já existentes em seu disco NVMe.
                    </div>
                </div>

                <div class="faq-item">
                    <div class="faq-question">
                        <span class="font-bold text-white italic">Quais as localizações disponíveis?</span>
                        <span class="faq-icon text-xl">+</span>
                    </div>
                    <div class="faq-answer">
                        Oferecemos infraestrutura global com datacenters nos Estados Unidos (Virgínia, Oregon), Europa (Alemanha, Reino Unido, França) e Ásia (Tóquio, Singapura). Você seleciona a melhor região para seu público-alvo no momento da compra.
                    </div>
                </div>
            </div>
        </section>

    </div>
</main>

<footer class="border-t border-white/5 py-16 text-center">
    <div class="container mx-auto px-6">
        <p class="text-[10px] uppercase tracking-[0.4em] text-zinc-600 mb-4">
            © {{ date('Y') }} CoelhoVPS — Excelência em Virtualização
        </p>
        <div class="flex justify-center gap-6 text-[10px] font-black uppercase tracking-widest text-zinc-500">
            <a href="{{ route('termos.mostrar') }}" class="hover:text-sky-400">Termos</a>
            <a href="{{ route('privacidade.mostrar') }}" class="hover:text-sky-400">Privacidade</a>
        </div>
    </div>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once:true, duration:800 });

    document.querySelectorAll('.faq-question').forEach(button => {
        button.addEventListener('click', () => {
            const faqItem = button.parentElement;
            
            // Opcional: Fechar outros itens ao abrir um novo
            document.querySelectorAll('.faq-item').forEach(item => {
                if (item !== faqItem) item.classList.remove('active');
            });

            faqItem.classList.toggle('active');
        });
    });
</script>

</body>
</html>