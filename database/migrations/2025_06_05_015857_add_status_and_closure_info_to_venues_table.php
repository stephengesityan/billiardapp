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
        Schema::table('venues', function (Blueprint $table) {
            $table->enum('status', ['open', 'close'])->default('open')->after('close_time');
            $table->text('close_reason')->nullable()->after('status');
            $table->date('reopen_date')->nullable()->after('close_reason');
            $table->time('original_open_time')->nullable()->after('reopen_date');
            $table->time('original_close_time')->nullable()->after('original_open_time');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['status', 'close_reason', 'reopen_date', 'original_open_time', 'original_close_time']);
        });
    }
};