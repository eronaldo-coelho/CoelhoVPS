<!DOCTYPE html>
<html lang="pt-br" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha — CoelhoVPS</title>
    @vite('resources/css/app.css')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <style>
        :root { --primary:#38bdf8; }
        body { background:#050505; }
        .bg-grid { background-image: radial-gradient(circle at 1px 1px, rgba(255,255,255,.04) 1px, transparent 0); background-size: 32px 32px; }
        .login-card { background: rgba(12, 12, 12, 0.88); backdrop-filter: blur(14px); border: 1px solid rgba(255, 255, 255, 0.06); }
        .campo-input { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255, 255, 255, 0.08); transition: 0.3s; color: white; }
        .campo-input:focus { border-color: var(--primary); outline: none; box-shadow: 0 0 15px rgba(56, 189, 248, 0.1); }
    </style>
</head>
<body class="text-zinc-400 antialiased bg-grid min-h-screen flex flex-col">

<header class="fixed inset-x-0 top-0 z-50 bg-black/70 backdrop-blur-xl border-b border-white/5">
    <div class="container mx-auto px-6 h-28 flex items-center justify-between">
        <a href="/"><img src="{{ asset('coelhovps.png') }}" class="h-16 w-auto"></a>
        <a href="{{ route('entrar.mostrar') }}" class="px-6 py-2 rounded-full border border-white/10 text-xs font-black uppercase hover:bg-white hover:text-black transition">Voltar ao Login</a>
    </div>
</header>

<main class="flex-grow flex items-center justify-center pt-32 pb-12 px-6">
    <div class="w-full max-w-[480px]">
        <div class="login-card rounded-[2.5rem] p-10 space-y-8" data-aos="fade-up">
            <div class="text-center">
                <h2 class="text-3xl font-black text-white italic uppercase">Recuperar <span class="text-sky-400">Senha</span></h2>
                <p class="text-[10px] text-zinc-500 font-bold uppercase tracking-[0.2em] mt-2">Enviaremos as instruções para seu e-mail</p>
            </div>

            @if (session('success'))
                <div class="bg-sky-400/10 border border-sky-400/20 p-4 rounded-2xl text-xs font-bold text-sky-400 uppercase leading-relaxed">
                    {{ session('success') }}
                </div>
            @endif

            <form class="space-y-5" action="{{ route('senha.esqueceu.enviar') }}" method="POST">
                @csrf
                <div>
                    <label class="text-[10px] font-black uppercase text-zinc-600 tracking-widest block mb-2">Informe seu E-mail</label>
                    <input type="email" name="email" required class="campo-input w-full px-5 py-4 rounded-2xl placeholder-zinc-600" placeholder="seu@email.com">
                </div>
                <button type="submit" class="w-full py-5 rounded-2xl bg-sky-400 text-black font-black uppercase tracking-widest text-xs transition transform hover:scale-[1.02] hover:bg-white">
                    Enviar Link de Recuperação
                </button>
            </form>
        </div>
    </div>
</main>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>AOS.init({ once:true });</script>
</body>
</html>