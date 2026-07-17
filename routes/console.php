<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Backup diário do banco. Isso só roda de fato se o scheduler do Laravel
// estiver registrado no cron do servidor (ver README, seção Instalação).
Schedule::command('backup:database')->dailyAt('02:00');

// Alerta semanal de manutenção preventiva (máquinas antigas / em manutenção
// prolongada) por e-mail aos admins, e opcionalmente por webhook.
Schedule::command('app:verificar-parque')->weeklyOn(1, '08:00');
