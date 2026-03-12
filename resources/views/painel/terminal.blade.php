<!DOCTYPE html>
<html lang="pt-br" class="h-full bg-gray-900">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Terminal: {{ $instancia->ip_v4 }}</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/xterm@5.3.0/css/xterm.min.css" />
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .terminal-container .xterm-viewport { overflow-y: auto !important; }
    </style>
</head>
<body class="h-full antialiased text-gray-200 overflow-hidden">

    <div class="flex flex-col h-screen p-4">
        <header class="mb-4 flex items-center justify-between">
            <h1 class="text-lg font-bold">Terminal SSH: <span class="text-blue-400 font-mono">{{ $instancia->ip_v4 }}</span></h1>
            <div id="status-indicator" class="flex items-center space-x-2 bg-gray-800 px-3 py-1 rounded-full">
                <div id="status-dot" class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></div>
                <span id="status-text" class="text-xs font-medium uppercase tracking-wider">Conectando...</span>
            </div>
        </header>

        <div class="flex-grow w-full bg-black rounded-lg shadow-2xl border border-gray-700 p-2 terminal-container">
            <div id="terminal" class="w-full h-full"></div>
        </div>
    </div>

    <!-- Scripts do Xterm.js -->
    <script src="https://cdn.jsdelivr.net/npm/xterm@5.3.0/lib/xterm.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/xterm-addon-fit@0.8.0/lib/xterm-addon-fit.min.js"></script>

    <script>
        const statusDot = document.getElementById('status-dot');
        const statusText = document.getElementById('status-text');

        // 1. Inicializa o Terminal
        const term = new Terminal({
            cursorBlink: true,
            fontSize: 14,
            fontFamily: 'ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace',
            theme: {
                background: '#000000',
                foreground: '#ffffff'
            }
        });

        const fitAddon = new FitAddon.FitAddon();
        term.loadAddon(fitAddon);
        term.open(document.getElementById('terminal'));
        fitAddon.fit();

        // 2. Conecta ao WebSocket
        const socket = new WebSocket('{!! $websocketUrl !!}');

        const updateStatus = (status, color, isPulsing) => {
            statusText.textContent = status;
            statusDot.className = `w-2 h-2 rounded-full ${color} ${isPulsing ? 'animate-pulse' : ''}`;
        };

        socket.onopen = () => {
            updateStatus('Online', 'bg-green-500', false);
            term.focus();
            
            // Envia o tamanho inicial da janela
            const { cols, rows } = term;
            socket.send(JSON.stringify({ type: 'resize', cols, rows }));
        };

        // Recebe dados do servidor -> Escreve no terminal
        socket.onmessage = (event) => {
            term.write(event.data);
        };

        // Envia dados do teclado -> Servidor
        term.onData((data) => {
            if (socket.readyState === WebSocket.OPEN) {
                socket.send(data);
            }
        });

        socket.onclose = () => {
            updateStatus('Desconectado', 'bg-red-500', false);
            term.writeln('\r\n\x1b[31m[Sessão Encerrada]\x1b[0m');
        };

        socket.onerror = (err) => {
            updateStatus('Erro', 'bg-red-500', false);
        };

        // Redimensionamento
        window.addEventListener('resize', () => {
            fitAddon.fit();
            if (socket.readyState === WebSocket.OPEN) {
                socket.send(JSON.stringify({
                    type: 'resize',
                    cols: term.cols,
                    rows: term.rows
                }));
            }
        });
    </script>
</body>
</html>