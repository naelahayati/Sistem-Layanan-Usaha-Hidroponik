<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('kunjungans', 'min_people')) {
            Schema::table('kunjungans', function (Blueprint $table) {
                $table->integer('min_people')->default(1)->after('max_people');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('kunjungans', 'min_people')) {
            Schema::table('kunjungans', function (Blueprint $table) {
                $table->dropColumn('min_people');
            });
        }
    }
};
