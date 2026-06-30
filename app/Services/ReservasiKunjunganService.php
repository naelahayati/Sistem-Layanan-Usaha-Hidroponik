<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservasiKunjunganService
{
    /**
     * Otomatis membatalkan reservasi kunjungan yang tanggal kunjungannya sudah hari H atau lewat,
     * tetapi statusnya masih belum dikonfirmasi/dibayar oleh admin atau user.
     */
    public static function autoCancelPassedReservations(): int
    {
        return DB::table('reservasi_kunjungan')
            ->whereDate('tanggal_reservasi', '<=', Carbon::today())
            ->whereIn('status_pembayaran', ['Menunggu Konfirmasi', 'Menunggu Pembayaran'])
            ->update([
                'status_pembayaran' => 'Dibatalkan',
                'updated_at' => now(),
            ]);
    }

    /**
     * Memeriksa dan memproses kedaluwarsa pembayaran QRIS atau pembatalan hari H
     */
    public static function expireIfNeeded(int $idReservasi): ?string
    {
        $row = DB::table('reservasi_kunjungan')
            ->where('id_reservasi', $idReservasi)
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
            DB::table('reservasi_kunjungan')
                ->where('id_reservasi', $idReservasi)
                ->update([
                    'status_pembayaran' => 'Expired',
                    'expires_at' => null,
                    'updated_at' => now(),
                ]);

            return 'Expired';
        }

        // 2. Cek Pembatalan Otomatis Hari H atau Lewat
        $visitDate = Carbon::parse($row->tanggal_reservasi)->startOfDay();
        if (
            Carbon::today() >= $visitDate
            && in_array($row->status_pembayaran, ['Menunggu Konfirmasi', 'Menunggu Pembayaran'])
        ) {
            DB::table('reservasi_kunjungan')
                ->where('id_reservasi', $idReservasi)
                ->update([
                    'status_pembayaran' => 'Dibatalkan',
                    'updated_at' => now(),
                ]);

            return 'Dibatalkan';
        }

        return $row->status_pembayaran;
    }
}
