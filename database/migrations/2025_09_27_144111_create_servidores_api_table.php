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
        Schema::create('servidores_api', function (Blueprint $table) {
            // Coluna de ID auto-incremento (chave primária). É uma boa prática ter sempre.
            $table->id();

            // Coluna para o "ProductId". Assumindo que seja um número inteiro.
            // Se puder ser texto, use $table->string('product_id');
            $table->string('product_id');

            // Coluna para o nome do produto "Product".
            $table->string('product');

            // Coluna para o "Disk Size". Usamos string para armazenar valores como "100 GB".
            $table->string('disk_size');

            // Coluna "servidor_id". Como ela se refere a outro servidor,
            // é uma chave estrangeira (foreign key).
            $table->integer('servidor_id');

            // Opcional, mas recomendado: Adiciona as colunas created_at e updated_at
            $table->timestamps();

            // Opcional, mas altamente recomendado: Definir a chave estrangeira
            // Isso assume que você tem uma tabela `servidores` com uma coluna `id`.
            // $table->foreign('servidor_id')->references('id')->on('servidores')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('servidores_api');
    }
};