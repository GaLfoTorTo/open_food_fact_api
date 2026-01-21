<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected $commands = [
        \App\Console\Commands\ProductDailyImport::class,
    ];
    /**
     * Define os comandos da aplicação
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    /**
     * Define o agendamento de tarefas (CRON)
     */
    protected function schedule(Schedule $schedule): void
    {
        //CRON DE IMPORTAÇÃO DIARIA DE PRODUTOS
        $schedule->command('products:import')->dailyAt('02:00');
    }
}