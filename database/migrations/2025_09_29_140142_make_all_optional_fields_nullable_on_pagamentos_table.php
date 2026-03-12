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
            $table->text('qr_code_base64')->nullable()->change();
            $table->text('qr_code_text')->nullable()->change();
            $table->dateTime('data_vencimento')->nullable()->change();
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
            // Este rollback assume que os campos eram NOT NULL antes
            $table->text('qr_code_base64')->nullable(false)->change();
            $table->text('qr_code_text')->nullable(false)->change();
            $table->dateTime('data_vencimento')->nullable(false)->change();
        });
    }
};