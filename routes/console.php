<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ADICIONE SEU AGENDAMENTO AQUI
Schedule::command('faturas:gerar-renovacao')->daily()->at('03:00'); // Executa todo dia às 3 da manhã