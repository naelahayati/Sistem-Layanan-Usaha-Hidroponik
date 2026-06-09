<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Carbon\Carbon;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Set timezone aplikasi agar real-time sesuai lokasi (Asia/Jakarta)
        date_default_timezone_set(config('app.timezone'));

        // Set bahasa Carbon ke Indonesia
        Carbon::setLocale('id');

        // Otomatisasi symlink storage:link jika folder public/storage belum ada
        try {
            if (!file_exists(public_path('storage'))) {
                \Illuminate\Support\Facades\Artisan::call('storage:link');
            }
        } catch (\Exception $e) {
            // Abaikan jika gagal (biasanya masalah permission pada beberapa environment Windows)
        }
    }
}
