<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('orders', 'midtrans_order_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('midtrans_order_id')->nullable()->after('status');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('orders', 'midtrans_order_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('midtrans_order_id');
            });
        }
    }
};
