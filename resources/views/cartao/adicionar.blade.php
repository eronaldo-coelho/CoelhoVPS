<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adicionar Cartão — CoelhoVPS</title>
    
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

        .card-pagamento {
            background: rgba(12, 12, 12, 0.88);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .campo-input, .mp-container {
            background: rgba(255, 255, 255, 0.03) !important;
            border: 1px solid rgba(255, 255, 255, 0.08) !important;
            transition: all 0.3s ease;
            color: white !important;
        }

        .mp-container {
            height: 54px;
            padding: 12px 16px;
            border-radius: 1rem;
            display: flex;
            align-items: center;
        }

        .campo-input:focus, .mp-container.focus {
            border-color: var(--primary) !important;
            background: rgba(56, 189, 248, 0.03) !important;
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.1);
            outline: none;
        }

        .mp-container iframe {
            width: 100%;
            height: 100%;
        }

        select.campo-input option {
            background: #111;
            color: white;
        }
    </style>
</head>

<body class="text-zinc-400 antialiased bg-grid min-h-screen flex flex-col">

<header class="fixed inset-x-0 top-0 z-50 bg-black/70 backdrop-blur-xl border-b border-white/5">
    <div class="container mx-auto px-6 h-28 flex items-center justify-between">
        <a href="/" class="flex items-center">
            <img src="{{ asset('coelhovps.png') }}" alt="CoelhoVPS" class="h-16 sm:h-20 md:h-24 w-auto transition-transform hover:scale-105">
        </a>

        <nav class="hidden md:flex gap-10 text-sm font-semibold">
            <a href="/sobre-nos" class="hover:text-sky-400 transition">Sobre</a>
            <a href="/suporte" class="hover:text-sky-400 transition">Suporte</a>
        </nav>

        <a href="{{ route('painel.dashboard') }}" class="px-6 py-2 rounded-full border border-sky-400/20 text-sky-400 text-xs font-black uppercase hover:bg-sky-400 hover:text-black transition">
            Meu Painel
        </a>
    </div>
</header>

<main class="flex-grow flex items-center justify-center pt-40 pb-20 px-6">
    <div class="w-full max-w-xl">
        <div class="card-pagamento rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-sky-400/5 space-y-8" data-aos="fade-up">
            
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-sky-400/10 rounded-2xl mb-6 text-sky-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-black text-white italic tracking-tight uppercase">Novo <span class="text-sky-400">Cartão</span></h2>
                <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest mt-4">Criptografia de ponta para sua segurança</p>
            </div>

            <div id="messages" class="empty:hidden"></div>

            <form id="form-checkout" class="space-y-6">
                <!-- Nome do Titular -->
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Nome no Cartão</label>
                    <input type="text" id="form-checkout__cardholderName" class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-700 focus:outline-none" placeholder="Ex: JOAO S SILVA" />
                </div>

                <!-- Documento -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Tipo Doc.</label>
                        <select id="form-checkout__identificationType" class="campo-input w-full px-5 h-[58px] text-white rounded-2xl focus:outline-none"></select>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Número Doc.</label>
                        <input type="text" id="form-checkout__identificationNumber" class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-700 focus:outline-none" placeholder="CPF ou CNPJ" />
                    </div>
                </div>

                <!-- Número do Cartão -->
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Número do Cartão</label>
                    <div id="form-checkout__cardNumber" class="mp-container"></div>
                </div>

                <!-- Data e CVV -->
                <div class="grid grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Validade</label>
                        <div id="form-checkout__expirationDate" class="mp-container"></div>
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">CVV</label>
                        <div id="form-checkout__securityCode" class="mp-container"></div>
                    </div>
                </div>

                <!-- Email (Readonly) -->
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1 opacity-50">E-mail do Titular</label>
                    <input type="email" id="form-checkout__cardholderEmail" value="{{ auth()->user()->email ?? '' }}" readonly class="campo-input w-full px-5 py-4 text-zinc-500 rounded-2xl cursor-not-allowed opacity-50 focus:outline-none" />
                </div>

                <select id="form-checkout__issuer" class="hidden"></select>
                <select id="form-checkout__installments" class="hidden"></select>

                <div class="pt-6">
                    <button type="submit" id="form-checkout__submit" class="w-full py-5 rounded-2xl bg-sky-400 text-black font-black uppercase tracking-[0.2em] text-xs transition transform hover:scale-[1.02] hover:bg-white active:scale-95 shadow-lg shadow-sky-400/20 disabled:opacity-50 disabled:cursor-wait">
                        <span id="button-text">Vincular Cartão com Segurança</span>
                        <div id="spinner" class="hidden justify-center">
                            <svg class="animate-spin h-5 w-5 text-black" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </div>
                    </button>
                </div>
            </form>

            <div class="flex items-center justify-center gap-6 pt-4 border-t border-white/5 opacity-40">
                <img src="https://logodownload.org/wp-content/uploads/2014/07/mastercard-logo-7.png" class="h-6 grayscale invert" alt="Master">
                <img src="https://logodownload.org/wp-content/uploads/2016/10/visa-logo-1.png" class="h-4 grayscale invert" alt="Visa">
                <img src="https://logodownload.org/wp-content/uploads/2014/07/american-express-logo-0.png" class="h-5 grayscale invert" alt="Amex">
            </div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        AOS.init({ duration: 800, once: true });
        
        const publicKey = "{{ config('services.mercadopago.public_key') }}";
        
        if (publicKey) {
            const mp = new MercadoPago(publicKey, { locale: 'pt-BR' });
            const cardForm = mp.cardForm({
                amount: "0",
                iframe: true,
                style: { theme: 'dark' },
                form: {
                    id: "form-checkout",
                    cardholderName: { id: "form-checkout__cardholderName", placeholder: "Nome como no cartão" },
                    cardholderEmail: { id: "form-checkout__cardholderEmail" },
                    cardNumber: { id: "form-checkout__cardNumber", placeholder: "0000 0000 0000 0000" },
                    expirationDate: { id: "form-checkout__expirationDate", placeholder: "MM/AA" },
                    securityCode: { id: "form-checkout__securityCode", placeholder: "CVV" },
                    identificationType: { id: "form-checkout__identificationType" },
                    identificationNumber: { id: "form-checkout__identificationNumber", placeholder: "CPF ou CNPJ" },
                    issuer: { id: "form-checkout__issuer" },
                    installments: { id: "form-checkout__installments" },
                },
                callbacks: {
                    onFormMounted: error => { if (error) console.warn("Mount Error: ", error); },
                    onFocus: (event) => document.getElementById(event.field).classList.add('focus'),
                    onBlur: (event) => document.getElementById(event.field).classList.remove('focus'),
                    onSubmit: event => {
                        event.preventDefault();
                        const btn = document.getElementById('form-checkout__submit');
                        const txt = document.getElementById('button-text');
                        const spi = document.getElementById('spinner');
                        
                        btn.disabled = true;
                        txt.classList.add('hidden');
                        spi.classList.remove('hidden');
                        spi.classList.add('flex');

                        const { token } = cardForm.getCardFormData();

                        fetch("{{ route('cartao.store') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}"
                            },
                            body: JSON.stringify({ token: token }),
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data && data.status === 'success' && data.redirect_url) {
                                window.location.href = data.redirect_url;
                            } else {
                                const msg = data.message || 'Falha ao processar cartão.';
                                document.getElementById('messages').innerHTML = `<div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-bold uppercase">${msg}</div>`;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            document.getElementById('messages').innerHTML = `<div class="p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-bold uppercase">Erro de conexão. Tente novamente.</div>`;
                        })
                        .finally(() => {
                            btn.disabled = false;
                            txt.classList.remove('hidden');
                            spi.classList.add('hidden');
                            spi.classList.remove('flex');
                        });
                    },
                },
            });
        }
    });
</script>

</body>
</html>