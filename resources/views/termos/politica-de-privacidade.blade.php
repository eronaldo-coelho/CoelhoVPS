<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade — CoelhoVPS</title>
    
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
                POLÍTICA DE <span class="text-sky-400">PRIVACIDADE</span>
            </h1>
            <p class="text-zinc-500 uppercase tracking-[0.3em] text-[10px] font-bold">
                Conformidade com a LGPD — Lei nº 13.709/2018
            </p>
        </div>

        <div class="max-w-4xl mx-auto card p-8 md:p-16 rounded-[3rem] terms-content shadow-2xl" data-aos="fade-up">
            
            <p>A <strong>CoelhoVPS</strong>, no exercício de suas atividades de infraestrutura tecnológica, reafirma seu compromisso inabalável com a transparência e a segurança das informações de seus clientes. Este documento detalha o tratamento conferido aos dados pessoais, em estrita observância à Lei Geral de Proteção de Dados (LGPD).</p>

            <h2>1. Coleta de Informações</h2>
            <p>Para a prestação de serviços de virtualização, coletamos exclusivamente os dados necessários para a formalização contratual e segurança da rede:</p>
            <ul>
                <li><strong>Dados Identificatórios:</strong> Nome completo, endereço eletrônico (e-mail) e registros de acesso à plataforma.</li>
                <li><strong>Dados de Faturamento:</strong> Informações fiscais necessárias para a emissão de comprovantes e processamento de pagamentos. Ressaltamos que <strong>não armazenamos</strong> dados sensíveis de cartões de crédito em nossa infraestrutura interna; estes são geridos de forma criptografada por gateways de pagamento certificados (PCI-DSS).</li>
                <li><strong>Dados Técnicos:</strong> Endereços IP de conexão, logs de sistema e metadados de tráfego, utilizados estritamente para prevenção de fraudes e mitigação de ataques cibernéticos.</li>
            </ul>

            <h2>2. Finalidade do Tratamento</h2>
            <p>As informações coletadas possuem finalidades específicas e fundamentadas na execução do contrato:</p>
            <ul>
                <li>Viabilizar o provisionamento automático e a gestão de instâncias VPS.</li>
                <li>Garantir a integridade da rede e o cumprimento das cláusulas de Uso Aceitável.</li>
                <li>Processar faturamentos e comunicações críticas sobre o estado do serviço.</li>
                <li>Prestar suporte técnico humano e personalizado via canais oficiais.</li>
            </ul>

            <h2>3. Segurança e Sigilo Absoluto</h2>
            <p>A <strong>CoelhoVPS</strong> emprega protocolos de segurança de nível industrial, incluindo criptografia SSL/TLS em todas as interfaces de gerenciamento. Nossos bancos de dados operam sob camadas de isolamento lógico e físico para impedir acessos não autorizados.</p>
            <p><strong>Nossa Política de Não Compartilhamento:</strong> É política institucional da CoelhoVPS <strong>jamais vender, alugar ou ceder</strong> dados pessoais de seus clientes a terceiros para finalidades comerciais ou de marketing. O compartilhamento de dados ocorre apenas mediante ordem judicial formal ou para parceiros operacionais indispensáveis (gateways de pagamento), sob rigorosos contratos de confidencialidade.</p>

            <h2>4. Direitos do Titular</h2>
            <p>Em conformidade com a LGPD, garantimos ao titular dos dados o pleno exercício de seus direitos, incluindo:</p>
            <ul>
                <li>Confirmação da existência de tratamento e acesso aos dados.</li>
                <li>Correção de dados incompletos ou inexatos através do Painel de Controle.</li>
                <li>Anonimização ou bloqueio de dados desnecessários.</li>
                <li>Exclusão definitiva de dados pessoais após o encerramento do ciclo contratual (observando prazos legais de retenção fiscal).</li>
            </ul>

            <h2>5. Cookies e Rastreamento</h2>
            <p>Utilizamos cookies estritamente funcionais, destinados apenas à manutenção da sessão do usuário e otimização da experiência no painel de gerenciamento. Não utilizamos tecnologias de rastreamento comportamental de terceiros para fins publicitários em nosso ambiente de produção.</p>

            <h2>6. Responsabilidade sobre Conteúdo</h2>
            <p>Embora a CoelhoVPS proteja os dados cadastrais do cliente, o conteúdo armazenado dentro das instâncias VPS (arquivos, bancos de dados de aplicações do cliente, etc.) é de <strong>responsabilidade privada e exclusiva do usuário</strong>. A CoelhoVPS não acessa o conteúdo interno dos servidores virtuais, operando sob o princípio de privacidade do ambiente virtualizado.</p>

            <h2>7. Canais de Contato</h2>
            <p>Para solicitações relacionadas à proteção de dados ou exercício de direitos de privacidade, o cliente deve formalizar o pedido através do e-mail <strong>comercial@coelhovps.com.br</strong> ou via ticket em nossa Central de Atendimento.</p>

            <div class="mt-16 pt-8 border-t border-white/5 text-center">
                <p class="text-sm italic">Ao contratar nossos serviços, você confirma ter lido e compreendido estas diretrizes de privacidade.</p>
            </div>

        </div>
    </div>
</main>

<footer class="border-t border-white/5 py-16 text-center">
    <div class="container mx-auto px-6">
        <p class="text-[10px] uppercase tracking-[0.4em] text-zinc-600">
            © {{ date('Y') }} CoelhoVPS — Proteção e Sigilo de Dados em Nível Global
        </p>
    </div>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once:true, duration:800 });
</script>

</body>
</html>