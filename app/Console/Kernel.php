<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command('security:check')->dailyAt('02:00');
        $schedule->command('check:bridge')
            ->timezone('Europe/Paris')
            ->everyFiveMinutes();
//        $schedule->command('update:saison')
//            ->timezone('Europe/Paris')
//            ->yearlyOn(9, 1, '04:00');
//        $schedule->command('update:annee')
//            ->timezone('Europe/Paris')
//            ->yearlyOn(1, 1, '03:00');
//        $schedule->command('update:vote')
//            ->timezone('Europe/Paris')
//            ->dailyAt('02:00');
//        $schedule->command('update:wpsite')
//            ->timezone('Europe/Paris')
//            ->dailyAt('22:00');
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
