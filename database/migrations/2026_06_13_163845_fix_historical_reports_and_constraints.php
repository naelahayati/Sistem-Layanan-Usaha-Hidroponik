<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Perbaiki data lama: Isi kolom snapshot (product_name, paket_name) yang masih kosong
        
        // Produk
        DB::statement("
            UPDATE order_items 
            JOIN products ON order_items.product_id = products.id 
            SET order_items.product_name = products.name 
            WHERE order_items.product_name IS NULL
        ");

        // Kunjungan
        DB::statement("
            UPDATE reservasi_kunjungan 
            JOIN kunjungans ON reservasi_kunjungan.id_kunjungan = kunjungans.id 
            SET reservasi_kunjungan.paket_name = kunjungans.name 
            WHERE reservasi_kunjungan.paket_name IS NULL
        ");

        // Magang
        DB::statement("
            UPDATE pendaftaran_magang 
            JOIN magangs ON pendaftaran_magang.id_magang = magangs.id 
            SET pendaftaran_magang.paket_name = magangs.name 
            WHERE pendaftaran_magang.paket_name IS NULL
        ");

        // 2. Ubah constraint agar data laporan TIDAK terhapus jika produk dihapus (Sistem Abadi)
        Schema::table('order_items', function (Blueprint $table) {
            // Drop foreign key lama
            $table->dropForeign(['product_id']);
            
            // Jadikan product_id nullable agar bisa 'set null'
            $table->unsignedBigInteger('product_id')->nullable()->change();
            
            // Tambah foreign key baru dengan onDelete set null
            $table->foreign('product_id')->references('id')->on('products')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('order_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->unsignedBigInteger('product_id')->nullable(false)->change();
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }
};
