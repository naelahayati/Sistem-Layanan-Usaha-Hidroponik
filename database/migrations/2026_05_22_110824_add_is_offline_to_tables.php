<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('orders') && ! Schema::hasColumn('orders', 'is_offline')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('is_offline')->default(false)->after('bukti_pembayaran');
            });
        }

        if (Schema::hasTable('reservasi_kunjungan') && ! Schema::hasColumn('reservasi_kunjungan', 'is_offline')) {
            Schema::table('reservasi_kunjungan', function (Blueprint $table) {
                $table->boolean('is_offline')->default(false)->after('bukti_pembayaran');
            });
        }

        if (Schema::hasTable('pendaftaran_magang') && ! Schema::hasColumn('pendaftaran_magang', 'is_offline')) {
            Schema::table('pendaftaran_magang', function (Blueprint $table) {
                $table->boolean('is_offline')->default(false)->after('bukti_pembayaran');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'is_offline')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('is_offline');
            });
        }

        if (Schema::hasColumn('reservasi_kunjungan', 'is_offline')) {
            Schema::table('reservasi_kunjungan', function (Blueprint $table) {
                $table->dropColumn('is_offline');
            });
        }

        if (Schema::hasColumn('pendaftaran_magang', 'is_offline')) {
            Schema::table('pendaftaran_magang', function (Blueprint $table) {
                $table->dropColumn('is_offline');
            });
        }
    }
};
