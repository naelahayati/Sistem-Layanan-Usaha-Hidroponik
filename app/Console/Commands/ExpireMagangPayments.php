<?php

namespace App\Console\Commands;

use App\Services\PendaftaranMagangService;
use Illuminate\Console\Command;

class ExpireMagangPayments extends Command
{
    protected $signature = 'magang:expire-payments';

    protected $description = 'Expire magang registrations that exceeded QRIS payment window';

    public function handle(): int
    {
        $count = PendaftaranMagangService::expireAllPending();
        $this->info("Expired {$count} magang registration(s).");

        return self::SUCCESS;
    }
}
