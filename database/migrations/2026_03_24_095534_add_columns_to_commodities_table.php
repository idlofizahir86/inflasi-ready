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
        Schema::table('commodities', function (Blueprint $table) {
            // Cek dulu apakah kolom sudah ada sebelum menambahkannya
            if (!Schema::hasColumn('commodities', 'category')) {
                $table->string('category')->after('name');
            }
            if (!Schema::hasColumn('commodities', 'trend')) {
                $table->string('trend')->nullable()->after('price');
            }
            if (!Schema::hasColumn('commodities', 'unit')) {
                $table->string('unit')->default('kg')->after('status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('commodities', function (Blueprint $table) {
            $table->dropColumn(['category', 'trend', 'unit']);
        });
    }
};
