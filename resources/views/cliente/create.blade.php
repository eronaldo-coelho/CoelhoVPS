<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completar Cadastro — CoelhoVPS</title>
    
    <link rel="icon" href="{{ asset('favicon.ico') }}">
    @vite('resources/css/app.css')
    
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Alpine.js + Mask Plugin -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/mask@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root { --primary:#38bdf8; }
        body { background:#050505; }

        .bg-grid {
            background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.04) 1px, transparent 0);
            background-size: 32px 32px;
        }

        .card-cadastro {
            background: rgba(12, 12, 12, 0.88);
            backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.06);
        }

        .campo-input {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s ease;
        }

        .campo-input:focus {
            border-color: var(--primary);
            background: rgba(56, 189, 248, 0.03);
            box-shadow: 0 0 15px rgba(56, 189, 248, 0.1);
        }
    </style>
</head>

<body class="text-zinc-400 antialiased bg-grid min-h-screen flex flex-col">

<header class="fixed inset-x-0 top-0 z-50 bg-black/70 backdrop-blur-xl border-b border-white/5">
    <div class="container mx-auto px-6 h-28 flex items-center justify-between">
        <a href="/" class="flex items-center">
            <img src="{{ asset('coelhovps.png') }}" alt="CoelhoVPS" class="h-16 sm:h-20 md:h-24 w-auto transition-transform hover:scale-105">
        </a>
        <div class="hidden md:block">
            <span class="text-[10px] font-black uppercase tracking-[0.3em] text-zinc-500">Ambiente Seguro de Cadastro</span>
        </div>
    </div>
</header>

<main class="flex-grow flex items-center justify-center pt-40 pb-20 px-6">
    <div class="w-full max-w-2xl">
        <div class="card-cadastro rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-sky-400/5 space-y-8" data-aos="fade-up">
            
            <div class="text-center">
                <h2 class="text-3xl md:text-4xl font-black text-white italic tracking-tight uppercase">Complete seu <span class="text-sky-400">Cadastro</span></h2>
                <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest mt-4">Dados necessários para processar seus pagamentos com segurança</p>
            </div>

            @if ($errors->any())
                <div class="bg-red-500/10 border border-red-500/20 text-red-400 px-5 py-4 rounded-2xl text-xs font-bold uppercase tracking-tight">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="space-y-6" action="{{ route('cliente.store') }}" method="POST" x-data>
                @csrf
                
                <!-- Nome e Sobrenome -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Nome</label>
                        <input id="first_name" name="first_name" type="text" required class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-700 focus:outline-none" placeholder="Ex: João" value="{{ old('first_name') }}">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Sobrenome</label>
                        <input id="last_name" name="last_name" type="text" required class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-700 focus:outline-none" placeholder="Ex: Silva" value="{{ old('last_name') }}">
                    </div>
                </div>

                <!-- CPF -->
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">CPF (Apenas números)</label>
                    <input x-mask="999.999.999-99" id="identification_number" name="identification_number" type="text" required class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-700 focus:outline-none" placeholder="000.000.000-00" value="{{ old('identification_number') }}">
                </div>

                <!-- Telefone -->
                <div class="grid grid-cols-4 md:grid-cols-12 gap-6">
                    <div class="col-span-1 md:col-span-3">
                        <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">DDD</label>
                        <input x-mask="99" id="phone_area_code" name="phone_area_code" type="text" required class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-700 focus:outline-none text-center" placeholder="11" value="{{ old('phone_area_code') }}">
                    </div>
                    <div class="col-span-3 md:col-span-9">
                        <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">WhatsApp / Telefone</label>
                        <input x-mask="99999-9999" id="phone_number" name="phone_number" type="text" required class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-700 focus:outline-none" placeholder="98888-8888" value="{{ old('phone_number') }}">
                    </div>
                </div>

                <!-- Endereço -->
                <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
                    <div class="md:col-span-4">
                        <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">CEP</label>
                        <input x-mask="99999-999" id="address_zip_code" name="address_zip_code" type="text" required class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-700 focus:outline-none" placeholder="00000-000" value="{{ old('address_zip_code') }}">
                    </div>
                    <div class="md:col-span-8">
                        <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Rua / Logradouro</label>
                        <input id="address_street_name" name="address_street_name" type="text" required class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-700 focus:outline-none" placeholder="Av. Principal" value="{{ old('address_street_name') }}">
                    </div>
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Número</label>
                    <input id="address_street_number" name="address_street_number" type="text" required oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-700 focus:outline-none" placeholder="123" value="{{ old('address_street_number') }}">
                </div>

                <div class="pt-6">
                    <button type="submit" class="w-full py-5 rounded-2xl bg-sky-400 text-black font-black uppercase tracking-[0.2em] text-xs transition transform hover:scale-[1.02] hover:bg-white active:scale-95 shadow-lg shadow-sky-400/20">
                        Salvar e Continuar para o Pagamento
                    </button>
                </div>
            </form>

            <div class="flex items-center justify-center gap-4 opacity-30 grayscale invert">
                <img src="https://logodownload.org/wp-content/uploads/2020/02/pix-bc-logo.png" class="h-4" alt="Pix">
                <div class="h-4 w-px bg-white/20"></div>
                <img src="https://logodownload.org/wp-content/uploads/2014/07/mastercard-logo-7.png" class="h-4" alt="Mastercard">
                <img src="https://logodownload.org/wp-content/uploads/2016/10/visa-logo-1.png" class="h-3" alt="Visa">
            </div>
        </div>
    </div>
</main>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ once:true, duration:800 });</script>

</body>
</html>