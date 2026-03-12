<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('contratos', function (Blueprint $table) {
            // Altera a coluna ENUM para incluir os novos status necessários
            $table->enum('status', [
                'pendente',
                'configurando', // Adicionado
                'ativo',
                'suspenso',
                'cancelado',
                'falha_autenticacao', // Adicionado
                'falha_provisionamento' // Adicionado
            ])->default('pendente')->change();
        });
    }

    public function down(): void {
        Schema::table('contratos', function (Blueprint $table) {
            // Reverte para o estado original se necessário
            $table->enum('status', [
                'pendente',
                'ativo',
                'suspenso',
                'cancelado'
            ])->default('pendente')->change();
        });
    }
};