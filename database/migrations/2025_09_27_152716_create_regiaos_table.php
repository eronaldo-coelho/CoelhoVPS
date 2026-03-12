<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('regioes', function (Blueprint $table) {
            $table->id();
            $table->string('regiao'); // Nome da região, ex: União Europeia
            $table->integer('latencia')->nullable(); // Latência em ms
            $table->string('regiao_id'); // Preço mensal
            $table->integer('porcentagem')->default(0); // Coluna de porcentagem
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('regioes');
    }
};
