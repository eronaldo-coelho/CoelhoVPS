<!DOCTYPE html>
<html lang="pt-br" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Painel') - CoelhoVPS</title>
    @vite('resources/css/app.css')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    <link rel="icon" href="{{ asset('favicon.ico') }}" type="image/x-icon">

    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/focus@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root { --primary: #38bdf8; }
        
        /* Personalização da barra de rolagem para combinar com o tema escuro */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #050505; }
        ::-webkit-scrollbar-thumb { background: #1f2937; border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: var(--primary); }
    </style>
</head>
<body class="bg-[#050505] text-zinc-400 font-sans antialiased overflow-x-hidden">

    <div x-data="{ sidebarOpen: window.innerWidth > 1024 }" @resize.window="sidebarOpen = window.innerWidth > 1024" class="flex min-h-screen bg-[#050505]">
        
        <!-- Overlay para mobile -->
        <div x-show="sidebarOpen" @click="sidebarOpen = false" class="fixed inset-0 z-30 bg-black/60 transition-opacity lg:hidden" x-cloak></div>

        <!-- SIDEBAR -->
        <aside 
            x-show="sidebarOpen"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in duration-300"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="fixed inset-y-0 left-0 z-40 flex w-72 flex-col border-r border-white/5 bg-black lg:static lg:inset-auto lg:translate-x-0"
            x-cloak>
            
            <!-- LOGO -->
            <div class="flex h-36 items-center justify-center border-b border-white/5 px-6">
                <a href="{{ route('painel.dashboard') }}" class="flex items-center">
                    <img 
                        src="{{ asset('coelhovps.png') }}" 
                        alt="CoelhoVPS" 
                        class="h-24 w-auto transition-transform hover:scale-105"
                    >
                </a>
            </div>

            <!-- NAVEGAÇÃO -->
            <nav class="mt-8 flex-1 space-y-2 px-4">
                <a href="{{ route('painel.dashboard') }}" 
                   class="group flex items-center rounded-xl px-4 py-3 text-sm font-bold uppercase tracking-wider transition-all {{ request()->routeIs('painel.dashboard') ? 'bg-sky-400/10 text-sky-400' : 'text-zinc-500 hover:bg-white/5 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('painel.dashboard') ? 'text-sky-400' : 'text-zinc-600 group-hover:text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    Dashboard
                </a>

                <a href="{{ route('painel.faturas.index') }}" 
                   class="group flex items-center rounded-xl px-4 py-3 text-sm font-bold uppercase tracking-wider transition-all {{ request()->routeIs('painel.faturas.index') ? 'bg-sky-400/10 text-sky-400' : 'text-zinc-500 hover:bg-white/5 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('painel.faturas.index') ? 'text-sky-400' : 'text-zinc-600 group-hover:text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                    Faturas
                </a>

                <a href="{{ route('painel.cartoes.index') }}" 
                   class="group flex items-center rounded-xl px-4 py-3 text-sm font-bold uppercase tracking-wider transition-all {{ request()->routeIs('painel.cartoes.index') ? 'bg-sky-400/10 text-sky-400' : 'text-zinc-500 hover:bg-white/5 hover:text-white' }}">
                    <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('painel.cartoes.index') ? 'text-sky-400' : 'text-zinc-600 group-hover:text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15A2.25 2.25 0 002.25 6.75v10.5A2.25 2.25 0 004.5 19.5z" /></svg>
                    Meus Cartões
                </a>

                <a href="{{ route('painel.perfil.edit') }}" 
                   class="group flex items-center rounded-xl px-4 py-3 text-sm font-bold uppercase tracking-wider transition-all {{ request()->routeIs('painel.perfil.edit') ? 'bg-sky-400/10 text-sky-400' : 'text-zinc-500 hover:bg-white/5 hover:text-white' }}">
                   <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('painel.perfil.edit') ? 'text-sky-400' : 'text-zinc-600 group-hover:text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" /></svg>
                    Meu Perfil
                </a>

                <a href="{{ route('painel.suporte.index') }}" 
                   class="group flex items-center rounded-xl px-4 py-3 text-sm font-bold uppercase tracking-wider transition-all {{ request()->routeIs('painel.suporte.index') ? 'bg-sky-400/10 text-sky-400' : 'text-zinc-500 hover:bg-white/5 hover:text-white' }}">
                   <svg class="mr-3 h-5 w-5 flex-shrink-0 {{ request()->routeIs('painel.suporte.index') ? 'text-sky-400' : 'text-zinc-600 group-hover:text-zinc-400' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a2 2 0 01-2-2V7a2 2 0 012-2h3.586a1 1 0 01.707.293l2.414 2.414a1 1 0 01.293.707V8z" /></svg>
                    Suporte
                </a>
            </nav>

            <!-- Rodapé da Sidebar -->
            <div class="p-6 border-t border-white/5 text-center">
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-zinc-700">© {{ date('Y') }} CoelhoVPS</span>
            </div>
        </aside>

        <!-- CONTEÚDO PRINCIPAL -->
        <div class="flex flex-1 flex-col overflow-x-hidden">
            
            <!-- HEADER TOPO -->
            <header class="sticky top-0 z-20 flex h-28 flex-shrink-0 items-center justify-between border-b border-white/5 bg-black/70 px-6 backdrop-blur-xl lg:px-10">
                <button @click="sidebarOpen = !sidebarOpen" type="button" class="rounded-xl p-3 text-zinc-500 bg-white/5 hover:text-sky-400 transition-colors lg:hidden">
                    <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>

                <div class="ml-auto flex items-center space-x-6">
                    <div class="hidden sm:flex flex-col items-end">
                        <span class="text-xs font-black uppercase tracking-widest text-zinc-500">Logado como</span>
                        <span class="text-sm font-bold text-white">{{ Auth::user()->name }}</span>
                    </div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="group flex h-12 w-12 items-center justify-center rounded-2xl bg-white/5 text-zinc-500 transition-all hover:bg-sky-400 hover:text-black focus:outline-none shadow-lg" title="Sair da Conta">
                            <svg class="h-5 w-5 transition-transform group-hover:scale-110" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        </button>
                    </form>
                </div>
            </header>

            <!-- MAIN VIEWPORT -->
            <main class="flex-1">
                <div class="container mx-auto px-6 py-10 lg:px-10">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>
    
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true, delay: 50 });
    </script>
</body>
</html>