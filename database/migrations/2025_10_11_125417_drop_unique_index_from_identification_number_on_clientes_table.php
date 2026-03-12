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
        Schema::table('clientes', function (Blueprint $table) {
            // O nome do índice é o que aparece no seu erro: 'clientes_identification_number_unique'
            $table->dropUnique('clientes_identification_number_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clientes', function (Blueprint $table) {
            // Recria a restrição única caso você precise reverter
            $table->unique('identification_number', 'clientes_identification_number_unique');
        });
    }
};