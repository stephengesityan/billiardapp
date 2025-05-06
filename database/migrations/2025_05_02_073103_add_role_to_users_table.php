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
        // Hanya menambahkan kolom venue_id dengan foreign key
        $table->unsignedBigInteger('venue_id')->nullable()->after('role');
        $table->foreign('venue_id')->references('id')->on('venues')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Menghapus foreign key dan kolom venue_id
        if (Schema::hasColumn('users', 'venue_id')) {
            $table->dropForeign(['venue_id']);  // Menghapus foreign key constraint
            $table->dropColumn('venue_id');     // Menghapus kolom venue_id
        }
    });
}
};
