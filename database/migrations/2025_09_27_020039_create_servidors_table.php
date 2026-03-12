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
    Schema::create('servidores', function (Blueprint $table) {
        $table->id(); // Coluna de ID auto-incremento (padrão)

        // Suas colunas personalizadas
        $table->integer('vCPU');                         // Ex: 4, 8, 16
        $table->string('ram');                           // Ex: "8 GB", "16 GB"
        $table->string('nvme');                          // Ex: "100 GB", "200 GB"
        $table->integer('snapshots')->default(0);        // Número de snapshots, com valor padrão 0
        $table->string('traffic');                       // Ex: "1 TB", "Ilimitado"
        $table->string('mais')->nullable();              // Coluna opcional para informações extras
        $table->decimal('valor', 8, 2);                  // Para dinheiro, use decimal. 8 dígitos no total, 2 após a vírgula (ex: 999999.99)
        $table->integer('desconto_percentual')->default(0); // Renomeado de "desconto_%"

        $table->timestamps(); // Colunas created_at e updated_at (padrão)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('servidors');
    }
};
