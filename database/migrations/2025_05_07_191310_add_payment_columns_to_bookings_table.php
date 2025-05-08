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
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('payment_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->decimal('total_amount', 10, 2)->nullable();
            $table->timestamp('payment_expired_at')->nullable();
            
            // Update status column if it exists
            if (Schema::hasColumn('bookings', 'status')) {
                $table->string('status')->default('pending')->change();
            } else {
                $table->string('status')->default('pending');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['payment_id', 'payment_method', 'total_amount', 'payment_expired_at']);
            // Don't drop status column as it might be used by other parts of the application
        });
    }
};
