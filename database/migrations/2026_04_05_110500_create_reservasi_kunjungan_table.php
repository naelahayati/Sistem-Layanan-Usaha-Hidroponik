<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('reservasi_kunjungan')) {
            return;
        }

        Schema::create('reservasi_kunjungan', function (Blueprint $table) {
            $table->integer('id_reservasi')->autoIncrement()->primary();
            $table->integer('id_user');
            $table->integer('id_kunjungan');
            $table->date('tanggal_reservasi');
            $table->integer('jumlah_peserta');
            $table->string('instansi', 255)->nullable();
            $table->decimal('total_harga', 15, 2);
            $table->string('metode_pembayaran', 50)->nullable();
            $table->string('status_pembayaran', 50)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('midtrans_order_id', 100)->nullable();
            $table->string('midtrans_token', 255)->nullable();
            $table->string('bukti_pembayaran', 255)->nullable();
            $table->boolean('is_offline')->default(false);

            // Perbaikan di sini: Otomatis menghandle created_at dan updated_at dengan aman
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservasi_kunjungan');
    }
};
