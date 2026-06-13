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
        Schema::table('order_items', function (Blueprint $table) {
            $table->string('product_name')->nullable()->after('product_id');
        });

        Schema::table('reservasi_kunjungan', function (Blueprint $table) {
            $table->string('paket_name')->nullable()->after('id_kunjungan');
        });

        Schema::table('pendaftaran_magang', function (Blueprint $table) {
            $table->string('paket_name')->nullable()->after('id_magang');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('product_name');
        });

        Schema::table('reservasi_kunjungan', function (Blueprint $table) {
            $table->dropColumn('paket_name');
        });

        Schema::table('pendaftaran_magang', function (Blueprint $table) {
            $table->dropColumn('paket_name');
        });
    }
};
