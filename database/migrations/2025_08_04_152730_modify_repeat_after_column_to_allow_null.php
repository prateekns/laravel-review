<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            // Modify repeat_after column to allow NULL values
            $table->unsignedTinyInteger('repeat_after')->nullable()->default(1)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            // Revert repeat_after column to NOT NULL
            $table->unsignedTinyInteger('repeat_after')->default(1)->change();
        });
    }
};
