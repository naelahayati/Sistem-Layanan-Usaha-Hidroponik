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
        Schema::create("orders", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("user_id");
            $table->decimal("total_produk", 15, 2);
            $table->decimal("ongkir", 15, 2);
            $table->decimal("grand_total", 15, 2);
            $table->string("metode_pembayaran");
            $table->string("metode_pengiriman");
            $table->text("alamat");
            $table->double("jarak")->nullable();
            $table->string("status")->default("Menunggu Pembayaran");
            $table->string("midtrans_order_id")->nullable();
            $table->string("qr_url")->nullable();
            $table->string("bukti_pembayaran")->nullable();
            $table->boolean("is_offline")->default(false);
            $table->timestamp("expires_at")->nullable();
            $table->timestamps();

            $table->foreign("user_id")->references("id")->on("users")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
