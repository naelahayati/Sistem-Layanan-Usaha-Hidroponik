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
        Schema::table('magangs', function (Blueprint $table) {
            $table->boolean('show_skill_description')->default(false)->after('is_wa_confirmation');
        });
    }

    public function down(): void
    {
        Schema::table('magangs', function (Blueprint $table) {
            $table->dropColumn('show_skill_description');
        });
    }
};
