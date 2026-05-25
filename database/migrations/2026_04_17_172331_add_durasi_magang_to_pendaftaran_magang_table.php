<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pendaftaran_magang') && ! Schema::hasColumn('pendaftaran_magang', 'durasi_magang')) {
            Schema::table('pendaftaran_magang', function (Blueprint $table) {
                $table->integer('durasi_magang')->default(1)->after('tanggal_magang');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('pendaftaran_magang', 'durasi_magang')) {
            Schema::table('pendaftaran_magang', function (Blueprint $table) {
                $table->dropColumn('durasi_magang');
            });
        }
    }
};
