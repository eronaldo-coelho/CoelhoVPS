<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('titulo') - Elite Performance</title>
    @vite('resources/css/app.css')
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
</head>
<body class="bg-black text-gray-300 font-sans antialiased">

    <header class="fixed top-0 left-0 right-0 z-50 bg-black/50 backdrop-blur-lg border-b border-lime-500/10">
        <div class="container mx-auto px-4">
            <div class="flex items-center justify-between h-20">
                <a href="/" class="text-2xl font-bold text-white hover:text-lime-400 transition-colors duration-300">
                    <span class="text-lime-400">&lt;</span>COELHO<span class="text-lime-400">/&gt;</span>VPS
                </a>
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="/sobre-nos" class="text-gray-300 hover:text-lime-400 transition-colors duration-300 font-medium">Sobre Nós</a>
                    <a href="/suporte" class="text-gray-300 hover:text-lime-400 transition-colors duration-300 font-medium">Suporte</a>
                </nav>
            </div>
        </div>
    </header>

    <main class="min-h-screen flex items-center justify-center pt-20 pb-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div class="text-center" data-aos="fade-down">
                <h2 class="text-center text-3xl font-extrabold text-white">
                    @yield('cabecalho')
                </h2>
            </div>
            @yield('conteudo')
        </div>
    </main>

    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        AOS.init({ duration: 800, once: true });
    </script>
</body>
</html>