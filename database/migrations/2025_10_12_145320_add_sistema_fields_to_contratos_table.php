<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            // Adiciona a coluna para a chave estrangeira do sistema
            $table->foreignId('sistema_id')
                  ->nullable()
                  ->after('regiao_id')
                  ->constrained('sistemas')
                  ->onDelete('set null');

            // Adiciona a coluna para guardar o nome do sistema (para relatórios e exibições rápidas)
            $table->string('sistema_nome')->nullable()->after('regiao_nome');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            // Remove a chave estrangeira e a coluna na ordem inversa da criação
            $table->dropForeign(['sistema_id']);
            $table->dropColumn(['sistema_id', 'sistema_nome']);
        });
    }
};