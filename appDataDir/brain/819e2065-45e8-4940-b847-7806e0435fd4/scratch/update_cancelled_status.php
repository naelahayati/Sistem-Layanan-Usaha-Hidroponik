<?php

use Illuminate\Support\Facades\DB;

// Update existing "Dibatalkan" to "Dibatalkan Pengguna" to clean up history
// Since we don't know who cancelled them, we assume user for now to clear the admin view.

DB::table('reservasi_kunjungan')->where('status_pembayaran', 'Dibatalkan')->update(['status_pembayaran' => 'Dibatalkan Pengguna']);
DB::table('pendaftaran_magang')->where('status_pembayaran', 'Dibatalkan')->update(['status_pembayaran' => 'Dibatalkan Pengguna']);
DB::table('orders')->where('status', 'Dibatalkan')->update(['status' => 'Dibatalkan Pengguna']);

echo "Database updated successfully.";
