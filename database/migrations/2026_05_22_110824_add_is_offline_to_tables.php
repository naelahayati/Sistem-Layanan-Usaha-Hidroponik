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
            $table->boolean('is_offline')->default(false)->after('bukti_pembayaran');
        });

        Schema::table('reservasi_kunjungan', function (Blueprint $table) {
            $table->boolean('is_offline')->default(false)->after('bukti_pembayaran');
        });

        Schema::table('pendaftaran_magang', function (Blueprint $table) {
            $table->boolean('is_offline')->default(false)->after('bukti_pembayaran');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('is_offline');
        });

        Schema::table('reservasi_kunjungan', function (Blueprint $table) {
            $table->dropColumn('is_offline');
        });

        Schema::table('pendaftaran_magang', function (Blueprint $table) {
            $table->dropColumn('is_offline');
        });
    }
};
