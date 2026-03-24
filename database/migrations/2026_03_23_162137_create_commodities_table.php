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
        Schema::create('commodities', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Contoh: Beras Medium
            $table->string('category'); // Contoh: Karbohidrat
            $table->decimal('price', 12, 2); // Contoh: 14500.00
            $table->string('unit')->default('kg'); // Satuan: kg, liter, dll
            $table->enum('status', [
                'stable',    // Harga normal/tetap
                'warning',   // Harga mulai naik (waspada)
                'critical',  // Harga naik tajam/sangat mahal
                'safe'       // Harga turun atau sangat murah
            ])->default('stable');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commodities');
    }
};
