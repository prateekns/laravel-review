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
        Schema::table('work_order_checklist_items', function (Blueprint $table) {
            $table->unsignedBigInteger('instance_id')->nullable()->after('work_order_id');
        });
    }

    /**
     * Reverse the migration.
     */
    public function down(): void
    {
        Schema::table('work_order_checklist_items', function (Blueprint $table) {
            $table->dropColumn('instance_id');
        });
    }
};
