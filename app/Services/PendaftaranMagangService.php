<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PendaftaranMagangService
{
    public const PAYMENT_MINUTES = 25;

    public static function autoCancelPassedMagangs(): int
    {
        return DB::table('pendaftaran_magang')
            ->whereDate('tanggal_magang', '<=', Carbon::today())
            ->whereIn('status_pembayaran', ['Menunggu Konfirmasi', 'Menunggu Pembayaran'])
            ->update([
                'status_pembayaran' => 'Dibatalkan',
                'expires_at' => null,
                'updated_at' => now(),
            ]);
    }

    public static function expireIfNeeded(int $idPendaftaran): ?string
    {
        $row = DB::table('pendaftaran_magang')
            ->where('id_pendaftaran', $idPendaftaran)
            ->first();

        if (!$row) {
            return null;
        }

        // 1. Cek Expired QRIS
        if (
            $row->status_pembayaran === 'Menunggu Pembayaran'
            && $row->expires_at
            && Carbon::parse($row->expires_at)->isPast()
        ) {
            DB::table('pendaftaran_magang')
                ->where('id_pendaftaran', $idPendaftaran)
                ->update([
                    'status_pembayaran' => 'Expired',
                    'expires_at' => null,
                    'updated_at' => now(),
                ]);

            return 'Expired';
        }

        // 2. Cek Pembatalan Otomatis Hari H atau Lewat jika status masih belum dikonfirmasi/dibayar
        $startDate = Carbon::parse($row->tanggal_magang)->startOfDay();
        if (
            Carbon::today() >= $startDate
            && in_array($row->status_pembayaran, ['Menunggu Konfirmasi', 'Menunggu Pembayaran'])
        ) {
            DB::table('pendaftaran_magang')
                ->where('id_pendaftaran', $idPendaftaran)
                ->update([
                    'status_pembayaran' => 'Dibatalkan',
                    'expires_at' => null,
                    'updated_at' => now(),
                ]);

            return 'Dibatalkan';
        }

        return $row->status_pembayaran;
    }

    public static function expireAllPending(): int
    {
        return DB::table('pendaftaran_magang')
            ->where('status_pembayaran', 'Menunggu Pembayaran')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update([
                'status_pembayaran' => 'Expired',
                'expires_at' => null,
                'updated_at' => now(),
            ]);
    }

    public static function canShowPayButton(object $row): bool
    {
        if ((float) $row->total_harga <= 0) {
            return false;
        }

        if ($row->status_pembayaran === 'Terkonfirmasi') {
            return true;
        }

        return $row->status_pembayaran === 'Menunggu Pembayaran'
            && ($row->metode_pembayaran ?? '') === 'qris'
            && $row->expires_at
            && Carbon::parse($row->expires_at)->isFuture();
    }

    public static function canAccessCheckout(object $row): bool
    {
        return $row->status_pembayaran === 'Terkonfirmasi';
    }

    public static function canAccessPaymentPage(object $row): bool
    {
        if ($row->status_pembayaran !== 'Menunggu Pembayaran') {
            return false;
        }

        if (!$row->expires_at) {
            return false;
        }

        return Carbon::parse($row->expires_at)->isFuture();
    }
}
