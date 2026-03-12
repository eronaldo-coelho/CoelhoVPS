<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class LimparBancoDados extends Command
{
    /**
     * A assinatura do comando.
     * @var string
     */
    protected $signature = 'db:clean-except';

    /**
     * A descrição do comando.
     * @var string
     */
    protected $description = 'Apaga todos os registros de todas as tabelas, exceto de uma lista pré-definida.';

    /**
     * Lista de tabelas que NÃO serão apagadas.
     * @var array
     */
    protected $tablesToKeep = [
        'servidores',
        'servidores_api',
        'instancias_vps',
        'migrations', // A tabela de migrations é sempre mantida por segurança
    ];

    /**
     * Executa o comando.
     */
    public function handle()
    {
        if (! $this->confirm('ATENÇÃO: Isso apagará TODOS os dados das tabelas, exceto as protegidas. Deseja continuar?')) {
            $this->info('Operação cancelada.');
            return;
        }

        Schema::disableForeignKeyConstraints();

        // --- INÍCIO DA MUDANÇA ---
        // Em vez de usar Doctrine, usamos uma query SQL direta para MySQL/MariaDB
        $tables = DB::select('SHOW TABLES');
        $dbName = DB::getDatabaseName();
        $tableKey = "Tables_in_{$dbName}"; // A chave do array retornado pelo SHOW TABLES

        foreach ($tables as $table) {
            $tableName = $table->$tableKey;
            // --- FIM DA MUDANÇA ---

            if (!in_array($tableName, $this->tablesToKeep)) {
                DB::table($tableName)->truncate();
                $this->line("Tabela <fg=yellow>{$tableName}</> foi limpa.");
            } else {
                $this->line("Tabela <fg=green>{$tableName}</> foi preservada.");
            }
        }

        Schema::enableForeignKeyConstraints();

        $this->info('Limpeza do banco de dados concluída com sucesso!');

        return 0;
    }
}