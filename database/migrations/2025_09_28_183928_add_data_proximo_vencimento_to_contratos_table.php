<?php
// database/migrations/xxxx_xx_xx_xxxxxx_add_data_proximo_vencimento_to_contratos_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->date('data_proximo_vencimento')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropColumn('data_proximo_vencimento');
        });
    }
};