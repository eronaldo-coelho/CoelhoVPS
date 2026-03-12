<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Servidor; // Importe o model

class ServidorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Servidor::create([
            'vCPU' => 2,
            'ram' => '4 GB',
            'nvme' => '50 GB',
            'snapshots' => 1,
            'traffic' => '1 TB',
            'mais' => 'Backup Semanal',
            'valor' => 49.90,
            'desconto_percentual' => 10,
        ]);

        Servidor::create([
            'vCPU' => 4,
            'ram' => '8 GB',
            'nvme' => '100 GB',
            'snapshots' => 2,
            'traffic' => '2 TB',
            'mais' => 'Backup Diário',
            'valor' => 89.90,
            'desconto_percentual' => 15,
        ]);

        Servidor::create([
            'vCPU' => 8,
            'ram' => '16 GB',
            'nvme' => '200 GB',
            'snapshots' => 3,
            'traffic' => 'Ilimitado',
            'mais' => 'Suporte Prioritário',
            'valor' => 159.90,
            'desconto_percentual' => 0, // Sem desconto
        ]);
    }
}