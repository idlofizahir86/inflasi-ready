<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('nama_supplier');
            $table->string('lokasi');
            $table->string('kontak')->nullable();
            $table->decimal('coordinate_lat', 10, 8)->nullable();
            $table->decimal('coordinate_long', 11, 8)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->enum('price_update_frequency', ['daily', 'weekly'])->default('weekly');
            $table->decimal('rating', 3, 2)->nullable();
            $table->integer('min_order_kg')->default(5);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
