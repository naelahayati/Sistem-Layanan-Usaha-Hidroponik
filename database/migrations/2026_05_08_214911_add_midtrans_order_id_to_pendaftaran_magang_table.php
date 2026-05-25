<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pendaftaran_magang') && ! Schema::hasColumn('pendaftaran_magang', 'midtrans_order_id')) {
            Schema::table('pendaftaran_magang', function (Blueprint $table) {
                $table->string('midtrans_order_id', 255)->nullable();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pendaftaran_magang', 'midtrans_order_id')) {
            Schema::table('pendaftaran_magang', function (Blueprint $table) {
                $table->dropColumn('midtrans_order_id');
            });
        }
    }
};
