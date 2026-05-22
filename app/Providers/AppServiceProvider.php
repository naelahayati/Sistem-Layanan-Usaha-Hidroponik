<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Otomatisasi symlink storage:link jika folder public/storage belum ada
        // Ini memastikan gambar selalu bisa diakses publik setelah sinkronisasi DB
        try {
            if (!file_exists(public_path('storage'))) {
                \Illuminate\Support\Facades\Artisan::call('storage:link');
            }
        } catch (\Exception $e) {
            // Abaikan jika gagal (biasanya masalah permission pada beberapa environment Windows)
        }
    }
}
