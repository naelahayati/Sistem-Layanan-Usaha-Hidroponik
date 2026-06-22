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

        // Laporan Harian Admin ke WhatsApp (Tes Tiap 5 Menit)
        $schedule->command('admin:daily-report')->everyMinute();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
