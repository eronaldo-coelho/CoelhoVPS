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
            // Coluna para identificar o tipo de pagamento (pix, credit_card, etc.)
            $table->string('tipo_pagamento')->after('payment_id_gateway')->default('pix')->comment('Identifica o meio de pagamento. Ex: pix, credit_card');

            // Coluna para o detalhe do status retornado pela gateway (ex: accredited)
            $table->string('status_detalhe')->nullable()->after('status')->comment('Status detalhado da gateway de pagamento');

            // Colunas específicas para Cartão de Crédito
            $table->string('metodo_pagamento')->nullable()->after('tipo_pagamento')->comment('Bandeira do cartão ou método. Ex: visa, mastercard');
            $table->string('card_last_four', 4)->nullable()->after('valor')->comment('Últimos 4 dígitos do cartão');
            $table->integer('parcelas')->nullable()->after('card_last_four')->comment('Número de parcelas da compra');
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
            $table->dropColumn([
                'tipo_pagamento',
                'status_detalhe',
                'metodo_pagamento',
                'card_last_four',
                'parcelas',
            ]);
        });
    }
};