<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            // Permite que as colunas específicas de PIX sejam nulas
            $table->text('qr_code_base64')->nullable()->change();
            $table->text('qr_code_text')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pagamentos', function (Blueprint $table) {
            // Reverte a alteração (caso precise dar rollback)
            $table->text('qr_code_base64')->nullable(false)->change();
            $table->text('qr_code_text')->nullable(false)->change();
        });
    }
};