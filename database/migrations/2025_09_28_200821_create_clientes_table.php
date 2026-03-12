<?php
// database/migrations/xxxx_xx_xx_xxxxxx_create_clientes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('customer_id_gateway')->nullable()->comment('ID do cliente no Mercado Pago');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_area_code', 4);
            $table->string('phone_number', 15);
            $table->string('identification_type', 10); // Ex: CPF, CNPJ
            $table->string('identification_number', 20)->unique();
            $table->string('address_zip_code', 10);
            $table->string('address_street_name');
            $table->string('address_street_number', 10);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};