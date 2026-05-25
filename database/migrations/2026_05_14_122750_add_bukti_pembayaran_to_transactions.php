<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'bukti_pembayaran')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('bukti_pembayaran')->nullable()->after('qr_url');
            });
        }

        if (Schema::hasTable('reservasi_kunjungan') && ! Schema::hasColumn('reservasi_kunjungan', 'bukti_pembayaran')) {
            Schema::table('reservasi_kunjungan', function (Blueprint $table) {
                $table->string('bukti_pembayaran', 255)->nullable();
            });
        }

        if (Schema::hasTable('pendaftaran_magang') && ! Schema::hasColumn('pendaftaran_magang', 'bukti_pembayaran')) {
            Schema::table('pendaftaran_magang', function (Blueprint $table) {
                $table->string('bukti_pembayaran', 255)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'bukti_pembayaran')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('bukti_pembayaran');
            });
        }

        if (Schema::hasColumn('reservasi_kunjungan', 'bukti_pembayaran')) {
            Schema::table('reservasi_kunjungan', function (Blueprint $table) {
                $table->dropColumn('bukti_pembayaran');
            });
        }

        if (Schema::hasColumn('pendaftaran_magang', 'bukti_pembayaran')) {
            Schema::table('pendaftaran_magang', function (Blueprint $table) {
                $table->dropColumn('bukti_pembayaran');
            });
        }
    }
};
