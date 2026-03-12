<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Definir Nova Senha — CoelhoVPS</title>
    @vite('resources/css/app.css')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root { --primary:#38bdf8; }
        body { background:#050505; }
        .bg-grid { background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.04) 1px, transparent 0); background-size: 32px 32px; }
        .login-card { background: rgba(12, 12, 12, 0.88); backdrop-filter: blur(14px); border: 1px solid rgba(255, 255, 255, 0.06); }
        .campo-input { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); transition: 0.3s; color: white; }
        .campo-input:focus { border-color: var(--primary); outline: none; }
    </style>
</head>
<body class="text-zinc-400 antialiased bg-grid min-h-screen flex flex-col">

<header class="fixed inset-x-0 top-0 z-50 bg-black/70 backdrop-blur-xl border-b border-white/5">
    <div class="container mx-auto px-6 h-28 flex items-center justify-between">
        <a href="/"><img src="{{ asset('coelhovps.png') }}" class="h-16"></a>
    </div>
</header>

<main class="flex-grow flex items-center justify-center pt-32 pb-12 px-6">
    <div class="w-full max-w-[480px]">
        <div class="login-card rounded-[2.5rem] p-10 space-y-8" data-aos="fade-up">
            <div class="text-center">
                <h2 class="text-3xl font-black text-white italic uppercase">Nova <span class="text-sky-400">Senha</span></h2>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em] mt-2">Escolha uma senha forte para sua segurança</p>
            </div>

            <form class="space-y-5" action="{{ route('senha.redefinir.processar') }}" method="POST">
                @csrf
                <input type="hidden" name="token" value="{{ $token }}">
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2">Nova Senha</label>
                    <input type="password" name="password" required class="campo-input w-full px-5 py-4 rounded-2xl" placeholder="••••••••">
                    @error('password') <span class="text-red-400 text-[10px] block mt-1 uppercase">{{ $message }}</span> @enderror
                </div>
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2">Confirmar Nova Senha</label>
                    <input type="password" name="password_confirmation" required class="campo-input w-full px-5 py-4 rounded-2xl" placeholder="••••••••">
                </div>
                <button type="submit" class="w-full py-5 rounded-2xl bg-sky-400 text-black font-black uppercase tracking-widest text-xs transition transform hover:scale-[1.02] hover:bg-white">
                    Atualizar Senha
                </button>
            </form>
        </div>
    </div>
</main>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ once:true });</script>
</body>
</html>