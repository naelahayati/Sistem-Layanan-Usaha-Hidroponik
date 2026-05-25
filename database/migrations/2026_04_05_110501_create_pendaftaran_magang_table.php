<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pendaftaran_magang')) {
            return;
        }

        Schema::create('pendaftaran_magang', function (Blueprint $table) {
            $table->integer('id_pendaftaran')->autoIncrement()->primary();
            $table->integer('id_user');
            $table->integer('id_magang');
            $table->string('pekerjaan', 100)->nullable();
            $table->date('tanggal_magang');
            $table->integer('durasi_magang')->default(1);
            $table->text('deskripsi_kemampuan')->nullable();
            $table->string('metode_pembayaran', 50)->nullable();
            $table->string('status_pembayaran', 50)->nullable();
            $table->string('snap_token', 255)->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->decimal('total_harga', 12, 2)->nullable();
            $table->string('midtrans_order_id', 255)->nullable();
            $table->string('bukti_pembayaran', 255)->nullable();
            $table->boolean('is_offline')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pendaftaran_magang');
    }
};
