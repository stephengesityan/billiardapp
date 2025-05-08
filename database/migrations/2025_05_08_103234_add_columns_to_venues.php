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
            $table->string('phone');
            $table->text('description');
            $table->time('open_time');
            $table->time('close_time');
            $table->dropColumn(['location', 'price']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('venues', function (Blueprint $table) {
            $table->dropColumn(['phone', 'description', 'open_time', 'close_time']);
            $table->string('location');
            $table->integer('price');
        });
    }
};
