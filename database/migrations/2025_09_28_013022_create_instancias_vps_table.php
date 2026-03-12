<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('instancias_vps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('contrato_id')->unique()->constrained()->onDelete('cascade');
            $table->bigInteger('instance_id_contabo');
            $table->string('display_name');
            $table->string('ip_v4')->nullable();
            $table->string('status');
            $table->string('root_password'); // Senha em texto plano, conforme solicitado
            $table->json('full_response_data')->nullable(); // Para guardar a resposta completa da API
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('instancias_vps');
    }
};