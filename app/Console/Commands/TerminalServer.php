<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Workerman\Worker;
use Workerman\Timer;
use App\Models\InstanciaVps;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use phpseclib3\Net\SSH2;

class TerminalServer extends Command
{
    protected $signature = 'terminal:serve {action=start} {--d}';
    protected $description = 'Inicia o servidor WebSocket para o terminal SSH usando Workerman.';

    public function handle()
    {
        global $argv;
        $argv[1] = $this->argument('action');
        if ($this->option('d')) {
            $argv[2] = '-d';
        }

        $port = 8080;
        $this->info("Iniciando servidor Workerman na porta " . $port);

        $worker = new Worker('websocket://0.0.0.0:' . $port);
        $worker->count = 1;

        $worker->onWorkerStart = function ($worker) {
            require_once base_path('vendor/autoload.php');
            $app = require base_path('bootstrap/app.php');
            $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();
        };

        $worker->onConnect = function ($connection) {
            // Este onConnect só prepara o terreno. A lógica real vai no onWebSocketConnect.
            $connection->onWebSocketConnect = function ($connection, $http_header) {
                try {
                    // AQUI ESTÁ A CORREÇÃO CRUCIAL
                    // Parseia a query string da requisição HTTP inicial
                    parse_str(parse_url($http_header, PHP_URL_QUERY), $query);
                    $instanceId = $query['instance_id'] ?? null;

                    if (empty($instanceId)) {
                        throw new \Exception("instance_id não foi fornecido na URL.");
                    }

                    DB::reconnect();
                    $instancia = InstanciaVps::find($instanceId);

                    if (!$instancia) {
                        throw new \Exception("Instância com ID '{$instanceId}' NÃO ENCONTRADA no banco de dados.");
                    }

                    $ssh = new SSH2($instancia->ip_v4);
                    if (!$ssh->login('root', $instancia->root_password)) {
                        throw new \Exception("Falha na autenticação SSH para o IP {$instancia->ip_v4}.");
                    }

                    $connection->ssh = $ssh;
                    $ssh->enablePTY('xterm');
                    $ssh->exec('bash -i');

                    $connection->timer = Timer::add(0.01, function () use ($connection) {
                        if (isset($connection->ssh) && $connection->ssh->isConnected()) {
                            $output = $connection->ssh->read();
                            if (!empty($output)) {
                                $connection->send($output);
                            }
                        } else {
                            $connection->close();
                        }
                    });

                } catch (\Throwable $e) {
                    $errorMessage = "ERRO: " . $e->getMessage();
                    Log::error($errorMessage . " | Arquivo: " . $e->getFile() . " | Linha: " . $e->getLine());
                    $connection->send($errorMessage);
                    $connection->close();
                }
            };
        };

        $worker->onMessage = function ($connection, $data) {
            if (isset($connection->ssh) && $connection->ssh->isConnected()) {
                $connection->ssh->write($data);
            }
        };

        $worker->onClose = function ($connection) {
            if (isset($connection->timer)) {
                Timer::del($connection->timer);
            }
            if (isset($connection->ssh) && $connection->ssh->isConnected()) {
                $connection->ssh->disconnect();
            }
        };

        Worker::runAll();
    }
}