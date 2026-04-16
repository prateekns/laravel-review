<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migration.
     */
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Add a standalone index on business_id for queries that only filter by business
            $table->index('business_id');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropIndex(['business_id']);
        });
    }
};
