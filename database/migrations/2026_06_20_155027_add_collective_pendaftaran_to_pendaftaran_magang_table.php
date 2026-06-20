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
        Schema::table('pendaftaran_magang', function (Blueprint $table) {
            $table->boolean('is_pendaftar_ikut')->default(true)->after('deskripsi_kemampuan');
            $table->text('list_nama_peserta')->nullable()->after('is_pendaftar_ikut');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pendaftaran_magang', function (Blueprint $table) {
            $table->dropColumn(['is_pendaftar_ikut', 'list_nama_peserta']);
        });
    }
};
