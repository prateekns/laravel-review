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
        Schema::table('customers', function (Blueprint $table) {
            // Add new columns
            $table->unsignedBigInteger('country_id')->nullable()->after('zip_code');

            // Add foreign key constraints
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('set null');

            // Make existing columns nullable
            $table->dropColumn('country');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Drop foreign key constraints
            $table->dropForeign(['country_id']);

            // Drop columns
            $table->dropColumn(['country_id']);

            // Make existing columns required again
            $table->string('country')->nullable(false)->change();
        });
    }
};
