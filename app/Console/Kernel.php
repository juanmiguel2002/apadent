<?php

namespace App\Console;

use App\Jobs\SendReminderEmailToClinic;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Programar el comando para que se ejecute diariamente a la medianoche
        // $schedule->job(new SendReminderEmailToClinic($pacienteId))->daily('08:00');
        $schedule->command('archivos:limpiar-temp')->daily();
        $schedule->command('cbct:clean-temp')->dailyAt('02:00');
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
