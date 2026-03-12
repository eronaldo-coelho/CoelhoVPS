<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('servidor_id')->constrained('servidores');
            $table->foreignId('servidor_api_id')->constrained('servidores_api');
            $table->foreignId('regiao_id')->constrained('regioes');

            // Armazena um 'snapshot' das especificações no momento da compra
            $table->string('vCPU');
            $table->string('ram');
            $table->string('disk_info'); // Ex: '75 GB NVMe'
            $table->integer('snapshots');
            $table->string('traffic');
            $table->string('regiao_nome'); // Ex: 'União Europeia'

            // Detalhes financeiros e de status
            $table->decimal('valor_total_mensal', 10, 2);
            $table->enum('status', ['pendente', 'ativo', 'suspenso', 'cancelado', 'configurando'])->default('pendente');
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};