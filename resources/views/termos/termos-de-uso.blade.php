<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos de Serviço — CoelhoVPS</title>
    
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

        .terms-content h2 {
            font-size: 1.25rem;
            font-weight: 900;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-top: 3rem;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .terms-content h2::after {
            content: "";
            flex: 1;
            height: 1px;
            background: rgba(56,189,248, 0.2);
        }

        .terms-content p, .terms-content li {
            color: #a1a1aa;
            line-height: 1.8;
            margin-bottom: 1.25rem;
            font-size: 0.95rem;
            text-align: justify;
        }

        .terms-content ul {
            list-style-type: none;
            padding-left: 1rem;
            border-left: 2px solid rgba(255,255,255,0.05);
            margin-bottom: 2rem;
        }

        .terms-content li::before {
            content: "•";
            color: var(--primary);
            font-weight: bold;
            display: inline-block; 
            width: 1em;
            margin-left: -1em;
        }

        .terms-content strong {
            color: #fff;
            font-weight: 700;
        }

        .highlight-box {
            background: rgba(56,189,248, 0.03);
            border: 1px solid rgba(56,189,248, 0.1);
            padding: 2rem;
            border-radius: 1.5rem;
            margin: 2.5rem 0;
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
            <a href="/suporte" class="hover:text-sky-400 transition">Suporte</a>
        </nav>

        <a href="{{ route('entrar.mostrar') }}" class="px-6 py-2 rounded-full border border-white/10 text-xs font-black uppercase hover:bg-white hover:text-black transition">
            Acessar Painel
        </a>
    </div>
</header>

<main class="pt-48 pb-24">
    <div class="container mx-auto px-6">
        
        <div class="text-center mb-16" data-aos="fade-up">
            <h1 class="text-4xl md:text-6xl font-black text-white italic mb-4">
                TERMOS DE <span class="text-sky-400">SERVIÇO</span>
            </h1>
            <p class="text-zinc-500 uppercase tracking-[0.3em] text-[10px] font-bold">
                Última atualização: Janeiro de 2025
            </p>
        </div>

        <div class="max-w-4xl mx-auto card p-8 md:p-16 rounded-[3rem] terms-content shadow-2xl" data-aos="fade-up">
            
            <p>Este documento estabelece as diretrizes jurídicas e operacionais que regem a relação entre a <strong>CoelhoVPS</strong> e seus contratantes. Ao utilizar nossa infraestrutura, o usuário declara ciência e anuência integral às cláusulas aqui dispostas.</p>

            <h2>1. Objeto do Serviço</h2>
            <p>A CoelhoVPS compromete-se a fornecer Servidores Virtuais Privados (VPS) com recursos de hardware pré-definidos no ato da contratação. O serviço compreende o provisionamento da instância, conectividade de rede e fornecimento de energia, operando sob o modelo de <strong>Autogestão (Unmanaged)</strong>, onde a administração do sistema operacional é de inteira responsabilidade do cliente.</p>

            <h2>2. Política de Reembolso e Rescisão</h2>
            <div class="highlight-box">
                <p class="text-white font-black italic mb-4 uppercase tracking-tight">Cláusula de Arrependimento e Garantia de Satisfação</p>
                <p>Em conformidade com as boas práticas comerciais e visando a confiança mútua, a CoelhoVPS estabelece os critérios para restituição de valores:</p>
                <ul class="mb-0">
                    <li><strong>Prazo Solicitacional:</strong> O pedido de reembolso deve ser formalizado em até <strong>10 (dez) dias corridos</strong> contados a partir da data de confirmação do pagamento inicial.</li>
                    <li><strong>Elegibilidade:</strong> Esta condição é estritamente restrita à <strong>primeira contratação</strong> realizada pelo usuário (mês de adesão). Renovações e ciclos subsequentes não fazem jus a esta garantia.</li>
                    <li><strong>Cálculo Proporcional:</strong> O reembolso não será integral. A CoelhoVPS restituirá o saldo remanescente após a <strong>dedução pro-rata die</strong> (desconto do valor correspondente aos dias em que o servidor esteve provisionado e à disposição do cliente).</li>
                    <li><strong>Canais de Atendimento:</strong> A solicitação deve ser enviada via E-mail ou, preferencialmente, através de nosso <strong>atendimento via WhatsApp</strong>, garantindo agilidade no processamento do protocolo.</li>
                </ul>
            </div>

            <h2>3. Atividades Proibidas e Uso Aceitável</h2>
            <p>É terminantemente proibida a utilização da infraestrutura para atividades que comprometam a integridade da rede ou violem a legislação vigente, incluindo, mas não se limitando a:</p>
            <ul>
                <li><strong>Abuso de Recursos:</strong> Mineração de criptomoedas, processos de codificação de vídeo intensos sem autorização prévia ou qualquer atividade que sature a CPU/IO de forma persistente.</li>
                <li><strong>Ilicitude:</strong> Hospedagem de materiais protegidos por direitos autorais sem autorização, conteúdo de pornografia infantil ou apologia ao crime.</li>
                <li><strong>Ataques Cibernéticos:</strong> Originar ou receber ataques de negação de serviço (DDoS), scanning de portas, phishing ou disseminação de malware.</li>
                <li><strong>Spam:</strong> Envio de comunicações em massa não solicitadas. O bloqueio de porta 25 é padrão em nossa rede para prevenção.</li>
            </ul>

            <h2>4. Pagamentos e Inadimplência</h2>
            <p>Os serviços operam sob o regime de pré-pagamento. A falta de quitação da fatura até a data de vencimento resultará na suspensão imediata da instância. A CoelhoVPS reserva-se o direito de <strong>excluir permanentemente</strong> os dados contidos em servidores com faturas vencidas há mais de 5 (cinco) dias, sem possibilidade de recuperação.</p>

            <h2>5. SLA (Garantia de Disponibilidade)</h2>
            <p>Garantimos um Uptime de rede e hardware de <strong>99,9%</strong> ao mês. Em casos de falha comprovada que excedam este limite, o cliente poderá solicitar créditos em conta proporcionais ao tempo de inatividade, excluindo-se janelas de manutenção programada e falhas causadas por má configuração do sistema operacional por parte do usuário.</p>

            <h2>6. Backup e Responsabilidade de Dados</h2>
            <p>A segurança e integridade dos dados armazenados no servidor virtual são de <strong>exclusiva responsabilidade do usuário</strong>. Embora ofereçamos ferramentas de Snapshots, orientamos que cópias externas de segurança sejam realizadas periodicamente. A CoelhoVPS não se responsabiliza por perdas de informações decorrentes de invasões, erros de software ou exclusões acidentais.</p>

            <h2>7. Modificações dos Termos</h2>
            <p>Reservamo-nos o direito de alterar estes termos visando a melhoria contínua dos processos. Alterações significativas serão comunicadas via e-mail. A continuidade do uso do serviço após tais alterações implica na aceitação tácita das novas diretrizes.</p>

            <div class="mt-16 pt-8 border-t border-white/5 text-center">
                <p class="text-sm italic">Para dúvidas adicionais, consulte nossa equipe jurídica através dos canais oficiais.</p>
            </div>

        </div>
    </div>
</main>

<footer class="border-t border-white/5 py-16 text-center">
    <div class="container mx-auto px-6">
        <p class="text-[10px] uppercase tracking-[0.4em] text-zinc-600">
            © {{ date('Y') }} CoelhoVPS — Empresa Brasileira de Infraestrutura Global
        </p>
    </div>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once:true, duration:800 });
</script>

</body>
</html>