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
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'alamat')) {
                $table->text('alamat')->nullable()->after('status');
            }
            if (! Schema::hasColumn('users', 'latitude')) {
                $table->decimal('latitude', 10, 8)->nullable()->after('alamat');
            }
            if (! Schema::hasColumn('users', 'longitude')) {
                $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            }
            if (! Schema::hasColumn('users', 'nohp')) {
                $table->string('nohp', 20)->nullable()->after('longitude');
            }
            if (! Schema::hasColumn('users', 'umur')) {
                $table->integer('umur')->nullable()->after('nohp');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = array_filter(
                ['alamat', 'latitude', 'longitude', 'nohp', 'umur'],
                fn (string $column) => Schema::hasColumn('users', $column)
            );

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
