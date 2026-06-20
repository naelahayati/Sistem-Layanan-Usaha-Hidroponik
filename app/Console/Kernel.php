<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{

    protected function schedule(Schedule $schedule)
    {
        // Cron QRIS expired
        $schedule->command('orders:check-expired')->everyMinute();
        $schedule->command('magang:expire-payments')->everyMinute();

        // Laporan Harian Admin ke WhatsApp (Jam 8 Pagi, 1 Siang, 7 Malam)
        $schedule->command('admin:daily-report')->dailyAt('08:00');
        $schedule->command('admin:daily-report')->dailyAt('13:00');
        $schedule->command('admin:daily-report')->dailyAt('19:00');
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
