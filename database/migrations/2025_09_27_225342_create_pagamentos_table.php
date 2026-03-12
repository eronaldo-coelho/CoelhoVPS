<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('contrato_id')->constrained()->onDelete('cascade');
            $table->string('payment_id_gateway')->unique();
            $table->string('status')->default('pending');
            $table->decimal('valor', 10, 2);
            $table->longText('qr_code_base64');
            $table->text('qr_code_text');
            $table->dateTime('data_vencimento');
            $table->dateTime('data_pagamento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagamentos');
    }
};