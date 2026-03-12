<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta — CoelhoVPS</title>
    
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

        .register-card {
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

        <a href="{{ route('entrar.mostrar') }}" class="px-6 py-2 rounded-full border border-sky-400/20 text-sky-400 text-xs font-black uppercase hover:bg-sky-400 hover:text-black transition">
            Login
        </a>
    </div>
</header>

<main class="flex-grow flex items-center justify-center pt-32 pb-12 px-6">
    <div class="w-full max-w-[540px]">
        <div class="register-card rounded-[2.5rem] p-10 shadow-2xl shadow-sky-400/5 space-y-8" data-aos="fade-up">
            <div class="text-center">
                <h2 class="text-3xl font-black text-white italic tracking-tight uppercase">Nova <span class="text-sky-400">Conta</span></h2>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.3em] mt-2">Junte-se à elite da virtualização</p>
            </div>

            <form class="space-y-5" action="{{ route('registrar.processar') }}" method="POST">
                @csrf
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Nome Completo</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required 
                        class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-600 focus:outline-none" 
                        placeholder="Ex: João Silva">
                    @error('name') <span class="text-red-400 text-[11px] font-bold mt-2 block ml-1 uppercase">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">E-mail</label>
                    <input id="email-address" name="email" type="email" value="{{ old('email') }}" required 
                        class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-600 focus:outline-none" 
                        placeholder="seu@email.com">
                    @error('email') <span class="text-red-400 text-[11px] font-bold mt-2 block ml-1 uppercase">{{ $message }}</span> @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div>
                        <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Senha</label>
                        <input id="password" name="password" type="password" required 
                            class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-600 focus:outline-none" 
                            placeholder="••••••••">
                    </div>
                    <div>
                        <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2 ml-1">Confirmar</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required 
                            class="campo-input w-full px-5 py-4 text-white rounded-2xl placeholder-zinc-600 focus:outline-none" 
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit" 
                    class="w-full py-5 rounded-2xl bg-sky-400 text-black font-black uppercase tracking-[0.2em] text-xs transition transform hover:scale-[1.02] hover:bg-white active:scale-95 shadow-lg shadow-sky-400/20">
                    Criar Minha Conta
                </button>
            </form>

            <div class="relative flex py-2 items-center">
                <div class="flex-grow border-t border-white/5"></div>
                <span class="flex-shrink mx-4 text-[10px] font-black text-zinc-700 uppercase tracking-widest">OU</span>
                <div class="flex-grow border-t border-white/5"></div>
            </div>

            <a href="{{ route('autenticacao.google') }}" 
                class="w-full flex items-center justify-center py-4 px-4 rounded-2xl border border-white/5 bg-white/[0.02] hover:bg-white/[0.05] transition-all group">
                <svg class="w-5 h-5 mr-3 transition group-hover:scale-110" viewBox="0 0 24 24">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l3.66-2.84z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span class="text-xs font-black uppercase text-white tracking-widest">Conta do Google</span>
            </a>
        </div>

        <!-- LINK PARA LOGIN -->
        <p class="mt-10 text-center text-[11px] font-bold uppercase tracking-widest text-zinc-600" data-aos="fade-up" data-aos-delay="200">
            Já possui uma conta ativa? 
            <a href="{{ route('entrar.mostrar') }}" class="text-sky-400 hover:text-white transition ml-1 underline decoration-sky-400/30 underline-offset-4">
                Fazer login agora
            </a>
        </p>
    </div>
</main>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ once:true, duration:800 });</script>
</body>
</html>