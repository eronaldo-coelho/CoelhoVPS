<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Finalizar Pedido - CoelhoVPS</title>
    
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite('resources/css/app.css')
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <script src="https://sdk.mercadopago.com/js/v2"></script>

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

        .selectable-card {
            cursor: pointer;
            border: 2px solid rgba(255,255,255,.05);
            transition: all 0.3s ease;
        }

        .selectable-card:hover {
            border-color: rgba(56,189,248, 0.4);
        }

        .selected {
            border-color: var(--primary) !important;
            background: rgba(56,189,248, 0.03);
            box-shadow: 0 0 25px rgba(56,189,248, 0.1);
        }

        .hidden-radio {
            display: none;
        }

        .check-indicator {
            width: 22px;
            height: 22px;
            border-radius: 50%;
            border: 2px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s;
        }

        .selected .check-indicator {
            background: var(--primary);
            border-color: var(--primary);
        }

        #card-details-container { 
            max-height: 0; 
            overflow: hidden; 
            transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1); 
        }

        .mp-cvv-container { 
            height: 48px; 
            background: rgba(255,255,255,0.03); 
            border: 1px solid rgba(255,255,255,0.1); 
            border-radius: 12px; 
            padding: 10px; 
            box-sizing: border-box; 
        }
    </style>
</head>

<body class="text-zinc-400 antialiased bg-grid min-h-screen">

<header class="fixed inset-x-0 top-0 z-50 bg-black/70 backdrop-blur-xl border-b border-white/5">
    <div class="container mx-auto px-6 h-28 flex items-center justify-between">
        <a href="/" class="flex items-center">
            <img 
                src="{{ asset('coelhovps.png') }}"
                alt="CoelhoVPS"
                class="h-16 sm:h-20 md:h-24 w-auto transition-transform hover:scale-105"
            >
        </a>

        <nav class="hidden md:flex gap-10 text-sm font-semibold">
            <a href="/sobre-nos" class="hover:text-sky-400 transition">Sobre</a>
            <a href="/suporte" class="hover:text-sky-400 transition">Suporte</a>
        </nav>

        <a href="{{ route('painel.dashboard') }}"
           class="px-6 py-2 rounded-full bg-white text-black text-xs font-black uppercase">
            Meu Painel
        </a>
    </div>
</header>

<main class="pt-40 pb-24">
    <div class="container mx-auto px-6">
        
        <div class="text-center mb-16" data-aos="fade-up">
            <h1 class="text-4xl md:text-6xl font-black text-white italic mb-4">
                QUASE <span class="text-sky-400">LÁ!</span>
            </h1>
            <p class="text-zinc-500 max-w-xl mx-auto uppercase tracking-widest text-xs font-bold">
                Confirme os detalhes e escolha sua forma de pagamento
            </p>
        </div>

        <form action="{{ route('checkout.processar') }}" method="POST" id="checkout-form">
            @csrf
            <input type="hidden" name="servidor_id" value="{{ $servidor->id }}">
            <input type="hidden" name="servidor_api_id" value="{{ $opcaoDisco->id }}">
            <input type="hidden" name="regiao_id" value="{{ $regiao->id }}">
            <input type="hidden" name="sistema_id" value="{{ $sistema->id }}">
            <input type="hidden" name="payment_token" id="payment_token">
            <input type="hidden" name="payment_method_id" id="payment_method_id">

            <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-12">
                
                <div class="lg:col-span-7 space-y-8" data-aos="fade-right">
                    
                    <div class="card p-8 rounded-[2rem]">
                        <h2 class="text-xl font-black text-white uppercase tracking-tight mb-6">Identificação</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-1">Nome Completo</label>
                                <p class="text-white font-bold">{{ $usuario->name }}</p>
                            </div>
                            <div>
                                <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-1">E-mail</label>
                                <p class="text-white font-bold">{{ $usuario->email }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="card p-8 rounded-[2rem]">
                        <h2 class="text-xl font-black text-white uppercase tracking-tight mb-6">Pagamento</h2>
                        
                        <div class="grid grid-cols-1 gap-4 mb-6">
                            <label class="selectable-card card p-5 rounded-2xl flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="p-2 bg-sky-400/10 rounded-lg">
                                        <svg class="w-6 h-6 text-sky-400" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 15l-5-5 1.41-1.41L11 14.17l7.59-7.59L20 8l-9 9z"/></svg>
                                    </div>
                                    <span class="text-white font-black italic">PIX <span class="text-[10px] text-zinc-500 font-bold not-italic ml-2 uppercase">Ativação Imediata</span></span>
                                </div>
                                <div class="check-indicator">
                                    <svg class="w-3 h-3 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <input type="radio" name="metodo_pagamento" value="pix" class="hidden-radio" {{ old('metodo_pagamento', $savedInput['metodo_pagamento'] ?? 'pix') == 'pix' ? 'checked' : '' }}>
                            </label>

                            <label id="credit-card-option" class="selectable-card card p-5 rounded-2xl flex items-center justify-between">
                                <div class="flex items-center gap-4">
                                    <div class="p-2 bg-sky-400/10 rounded-lg">
                                        <svg class="w-6 h-6 text-sky-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M21 4H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h18c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 14H3V6h18v12zm-9-7H5v2h7v-2zm-4 4H5v2h3v-2zm8-4h-3v2h3v-2zm0 4h-3v2h3v-2z"/></svg>
                                    </div>
                                    <span class="text-white font-black italic">CARTÃO DE CRÉDITO</span>
                                </div>
                                <div class="check-indicator">
                                    <svg class="w-3 h-3 text-black" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="4" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <input type="radio" name="metodo_pagamento" id="payment_credit_card" value="credit_card" class="hidden-radio" {{ old('metodo_pagamento', $savedInput['metodo_pagamento'] ?? '') == 'credit_card' ? 'checked' : '' }}>
                            </label>
                        </div>

                        <div id="card-details-container">
                            @if($cards->isNotEmpty())
                                <div class="space-y-3 mb-6">
                                    @foreach($cards as $card)
                                    <label class="selectable-card bg-white/5 border-white/5 p-4 rounded-xl flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <img src="{{ $card->payment_method->thumbnail }}" class="h-5">
                                            <span class="text-white font-bold text-sm">•••• {{ $card->last_four_digits }}</span>
                                        </div>
                                        <input type="radio" name="card_id" value="{{ $card->id }}" class="hidden-radio" data-payment-method-id="{{ $card->payment_method->id }}" {{ old('card_id', $savedInput['card_id'] ?? ($loop->first ? $card->id : null)) == $card->id ? 'checked' : '' }}>
                                    </label>
                                    @endforeach
                                </div>
                                <div id="security-code-wrapper" class="p-4 bg-sky-400/5 rounded-2xl border border-sky-400/10">
                                    <label class="text-[10px] font-black uppercase text-sky-400 tracking-widest block mb-3">Código CVV</label>
                                    <div id="securityCode-container" class="mp-cvv-container"></div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5" data-aos="fade-left">
                    <div class="sticky top-32 card p-8 rounded-[2.5rem] border-sky-400/20 shadow-2xl shadow-sky-400/5">
                        <h2 class="text-2xl font-black text-white italic mb-8">Pedido</h2>
                        
                        <div class="space-y-3 mb-8">
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-500 font-bold">Instância</span>
                                <span class="text-white font-black italic">{{ $servidor->vCPU }} Core</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-500 font-bold">Armazenamento</span>
                                <span class="text-white font-black italic">{{ $opcaoDisco->disk_size }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-500 font-bold">Local</span>
                                <span class="text-white font-black italic">{{ $regiao->regiao }}</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-zinc-500 font-bold">Sistema</span>
                                <span class="text-white font-black italic">{{ $sistema->description }}</span>
                            </div>
                        </div>

                        <div class="pt-6 border-t border-white/5 mb-10">
                            <div class="flex justify-between items-end">
                                <span class="text-white font-black italic">TOTAL</span>
                                <div class="text-right">
                                    <span class="text-4xl font-black text-sky-400 block leading-none">R$ {{ number_format($valorTotal, 2, ',', '.') }}</span>
                                    <span class="text-[10px] text-zinc-500 uppercase font-bold">Cobrança Mensal</span>
                                </div>
                            </div>
                        </div>

                        <button type="submit" class="w-full py-5 rounded-2xl bg-sky-400 text-black font-black uppercase tracking-[0.2em] text-xs transition transform hover:scale-[1.02] hover:bg-white active:scale-95">
                            Confirmar e Pagar
                        </button>

                        <p class="mt-6 text-[10px] text-center text-zinc-600 font-bold uppercase tracking-widest">
                            Ambiente Criptografado • SSL Seguro
                        </p>
                    </div>
                </div>
            </div>
        </form>
    </div>
</main>

<footer class="border-t border-white/5 py-16 text-center">
    <p class="text-[10px] uppercase tracking-[0.4em] text-zinc-600">
        © {{ date('Y') }} CoelhoVPS — Excelência em Infraestrutura
    </p>
</footer>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    AOS.init({ once:true, duration:800 });

    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('checkout-form');
        const creditCardOption = document.getElementById('credit-card-option');
        const paymentCreditCardRadio = document.getElementById('payment_credit_card');
        const cardDetailsContainer = document.getElementById('card-details-container');
        const hasCards = {{ $cards->isNotEmpty() ? 'true' : 'false' }};
        const mp = new MercadoPago("{{ config('services.mercadopago.public_key') }}", { locale: 'pt-BR' });
        let securityCodeElement;

        function updateStyles() {
            document.querySelectorAll('.selectable-card').forEach(card => {
                const radio = card.querySelector('input[type="radio"]');
                if (radio && radio.checked) card.classList.add('selected');
                else card.classList.remove('selected');
            });

            if (paymentCreditCardRadio.checked && hasCards) {
                cardDetailsContainer.style.maxHeight = "500px";
                if (!securityCodeElement) {
                    securityCodeElement = mp.fields.create('securityCode', { 
                        style: { 
                            theme: 'dark',
                            placeholder: { color: '#52525b' }
                        } 
                    }).mount('securityCode-container');
                }
            } else {
                cardDetailsContainer.style.maxHeight = '0';
            }
        }

        form.addEventListener('change', updateStyles);
        
        creditCardOption.addEventListener('click', (e) => {
            if (!hasCards) {
                e.preventDefault();
                window.location.href = "{{ route('cartao.create') }}";
            }
        });

        form.addEventListener('submit', async (e) => {
            if (paymentCreditCardRadio.checked && hasCards) {
                e.preventDefault();
                const btn = form.querySelector('button[type="submit"]');
                btn.disabled = true;
                btn.textContent = 'VALIDANDO...';

                try {
                    const selectedCard = document.querySelector('input[name="card_id"]:checked');
                    document.getElementById('payment_method_id').value = selectedCard.dataset.paymentMethodId;
                    
                    const token = await mp.fields.createCardToken({ cardId: selectedCard.value });
                    document.getElementById('payment_token').value = token.id;
                    
                    form.submit();
                } catch (err) {
                    alert('Erro ao validar cartão. Verifique o CVV.');
                    btn.disabled = false;
                    btn.textContent = 'Confirmar e Pagar';
                }
            }
        });

        updateStyles();
    });
</script>

</body>
</html>