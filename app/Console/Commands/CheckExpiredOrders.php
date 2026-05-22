<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Http\Controllers\OrderController;

class CheckExpiredOrders extends Command
{
    protected $signature = 'orders:check-expired';
    protected $description = 'Check expired QRIS orders and restore cart';

    public function handle()
{
    $expiredOrders = Order::where('status', 'Menunggu Pembayaran')
        ->where('metode_pembayaran', 'qris')
        ->where('expires_at', '<', now())
        ->get();

    foreach ($expiredOrders as $order) {
        app(\App\Http\Controllers\NazframController::class)
            ->restoreToCart($order);
    }

    $this->info('Expired orders checked');
}
}
