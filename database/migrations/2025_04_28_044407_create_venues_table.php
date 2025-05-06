<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
            $table->id();
            $table->string('name');       // Nama venue
            $table->string('location');   // Lokasi venue (misalnya: Jakarta)
            $table->string('address');    // Alamat venue
            $table->integer('price');
            $table->string('image')->nullable();  // Gambar venue (opsional)
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
