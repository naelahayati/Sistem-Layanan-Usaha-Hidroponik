<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('bukti_pembayaran')->nullable()->after('qr_url');
        });

        Schema::table('reservasi_kunjungan', function (Blueprint $table) {
            $table->string('bukti_pembayaran')->nullable();
        });

        Schema::table('pendaftaran_magang', function (Blueprint $table) {
            $table->string('bukti_pembayaran')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('bukti_pembayaran');
        });

        Schema::table('reservasi_kunjungan', function (Blueprint $table) {
            $table->dropColumn('bukti_pembayaran');
        });

        Schema::table('pendaftaran_magang', function (Blueprint $table) {
            $table->dropColumn('bukti_pembayaran');
        });
    }
};
