<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Senha — CoelhoVPS</title>
    
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

        .login-card {
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

        <nav class="hidden md:flex gap-10 text-sm font-semibold">
            <a href="/sobre-nos" class="hover:text-sky-400 transition">Sobre</a>
            <a href="/suporte" class="hover:text-sky-400 transition">Suporte</a>
        </nav>
    </div>
</header>

<main class="flex-grow flex items-center justify-center pt-32 pb-12 px-6">
    <div class="w-full max-w-[480px]">
        <div class="login-card rounded-[2.5rem] p-10 shadow-2xl shadow-sky-400/5 space-y-8" data-aos="fade-up">
            
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-sky-400/10 rounded-2xl mb-6 text-sky-400">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 00-2 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <h2 class="text-3xl font-black text-white italic tracking-tight uppercase">Definir <span class="text-sky-400">Senha</span></h2>
                <p class="text-[11px] text-zinc-500 font-bold uppercase tracking-widest mt-4 leading-relaxed">
                    Você se conectou via Google. Por segurança, crie uma senha para sua conta.
                </p>
            </div>

            <form class="space-y-5" action="{{ route('senha.definir.processar') }}" method="POST">
                @csrf
                
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Nova Senha</label>
                    <input id="password" name="password" type="password" required 
                        class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-600 focus:outline-none" 
                        placeholder="••••••••">
                    @error('password') <span class="text-red-400 text-[11px] font-bold mt-2 block ml-1 uppercase">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Confirmar Senha</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required 
                        class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-600 focus:outline-none" 
                        placeholder="••••••••">
                </div>

                <div class="pt-2">
                    <button type="submit" 
                        class="w-full py-5 rounded-2xl bg-sky-400 text-black font-black uppercase tracking-[0.2em] text-xs transition transform hover:scale-[1.02] hover:bg-white active:scale-95 shadow-lg shadow-sky-400/20">
                        Salvar e Continuar
                    </button>
                </div>
            </form>

            <p class="text-[10px] text-center text-zinc-600 font-bold uppercase tracking-widest">
                Proteção por Criptografia de Ponta
            </p>
        </div>
    </div>
</main>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ once:true, duration:800 });</script>
</body>
</html>