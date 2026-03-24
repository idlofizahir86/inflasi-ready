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
        Schema::table('regions', function (Blueprint $table) {
            if (!Schema::hasColumn('regions', 'status')) {
                $table->string('status')->nullable()->after('slug');
            }
            if (!Schema::hasColumn('regions', 'color')) {
                $table->string('color')->nullable()->after('status');
            }
            if (!Schema::hasColumn('regions', 'textColor')) {
                $table->string('textColor')->nullable()->after('color');
            }
        });
    }

    public function down(): void
    {
        Schema::table('regions', function (Blueprint $table) {
            $table->dropColumn(['status', 'color', 'textColor']);
        });
    }
};
