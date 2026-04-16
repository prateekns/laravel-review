<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Add credit_balance column to businesses table.
     * This column will store the business's available credit balance.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->integer('credit_balance')
                ->default(0)
                ->after('status')
                ->comment('Available credit balance for the business in cents');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::table('businesses', function (Blueprint $table) {
            $table->dropColumn('credit_balance');
        });
    }
};
