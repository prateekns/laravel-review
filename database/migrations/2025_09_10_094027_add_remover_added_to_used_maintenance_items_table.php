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
        Schema::table('used_maintenance_items', function (Blueprint $table) {
            $table->string('remover_added')->nullable()->after('unit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('used_maintenance_items', function (Blueprint $table) {
            $table->dropColumn('remover_added');
        });
    }
};
