<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRescheduleColumnsToBookingsTable extends Migration
{
    public function up()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->boolean('has_rescheduled')->default(false);
            $table->timestamp('original_start_time')->nullable();
            $table->timestamp('original_end_time')->nullable();
            $table->unsignedBigInteger('original_table_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['has_rescheduled', 'original_start_time', 'original_end_time', 'original_table_id']);
        });
    }
}