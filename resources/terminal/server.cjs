// server.cjs - Versão Definitiva

const WebSocket = require('ws');
const { Client } = require('ssh2');
const { URL } = require('url');

const wss = new WebSocket.Server({ port: 8080 });

console.log('Servidor de Terminal Node.js iniciado na porta 8080.');
console.log('Aguardando conexões do seu painel Laravel...');

wss.on('connection', (ws, req) => {
    const requestUrl = new URL(req.url, `http://${req.headers.host}`);
    const params = requestUrl.searchParams;
    const ip = params.get('ip');
    const password = params.get('password');

    if (!ip || !password) {
        console.error(`ERRO: IP ou senha não fornecidos. URL: "${req.url}"`);
        ws.send('ERRO: IP ou senha não fornecidos.');
        ws.close();
        return;
    }

    const conn = new Client();
    let stream;

    conn.on('ready', () => {
        console.log(`[${ip}] Conexão SSH estabelecida.`);
        
        conn.shell((err, sshStream) => {
            if (err) {
                console.error(`[${ip}] Erro ao iniciar o shell:`, err);
                ws.send(`ERRO: Não foi possível iniciar o shell SSH.`);
                ws.close();
                return;
            }
            stream = sshStream;

            // Ponte: Dados do SSH -> Navegador
            stream.on('data', (data) => {
                try {
                    if (ws.readyState === WebSocket.OPEN) {
                        ws.send(data.toString('utf8'));
                    }
                } catch (e) {
                    console.error(`[${ip}] Erro ao enviar dados para WS:`, e.message);
                }
            });

            // Ponte: Dados do Navegador -> SSH
            ws.on('message', (rawMsg) => {
                // Tenta analisar a mensagem como JSON.
                try {
                    const msg = JSON.parse(rawMsg);
                    // Se for uma mensagem de redimensionamento, ajusta a janela PTY.
                    if (msg.type === 'resize' && msg.cols && msg.rows) {
                        // O stream tem o método setWindow para isso.
                        stream.setWindow(msg.rows, msg.cols);
                        return; // Impede que a mensagem seja escrita no shell.
                    }
                } catch (e) {
                    // Se não for JSON, é um comando normal do usuário.
                    // Simplesmente escreve no shell.
                    stream.write(rawMsg);
                }
            });

            stream.on('close', () => {
                console.log(`[${ip}] Stream SSH fechado.`);
                conn.end();
            });
        });
    }).on('error', (err) => {
        console.error(`[${ip}] Erro de conexão SSH:`, err.message);
        ws.send(`ERRO: Falha na conexão SSH - ${err.message}`);
        ws.close();
    });

    ws.on('close', () => {
        if (conn) {
            console.log(`[${ip}] Conexão WebSocket fechada. Desconectando do SSH.`);
            conn.end();
        }
    });
    
    console.log(`[${ip}] Nova conexão recebida. Tentando conectar...`);
    conn.connect({ host: ip, port: 22, username: 'root', password: password });
});