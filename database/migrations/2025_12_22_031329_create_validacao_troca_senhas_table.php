<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('validacao_troca_senhas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('email');
            $table->string('token', 32)->unique();
            $table->boolean('usado')->default(false);
            $table->timestamp('data_horario');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('validacao_troca_senhas');
    }
};