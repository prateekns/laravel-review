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
        Schema::table('work_order_checklist_items', function (Blueprint $table) {
            $table->boolean('is_visible')->default(false)->after('description')->index();
            $table->boolean('is_custom')->default(false)->after('is_visible');
            $table->boolean('is_default')->default(false)->after('is_custom');
            $table->integer('sort_order')->default(0)->after('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order_checklist_items', function (Blueprint $table) {
            $table->dropIndex(['is_visible']);
            $table->dropColumn(['is_visible', 'is_custom', 'is_default', 'sort_order']);
        });
    }
};
