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
        // Work Orders table indexes - only adding new ones
        try {
            Schema::table('work_orders', function (Blueprint $table) {
                // Index for technician's work orders lookup with active status
                $table->index(['technician_id', 'is_active', 'deleted_at'], 'idx_work_tech_status');

                // Index for date range queries on non-recurring orders
                $table->index(['is_recurring', 'preferred_start_date'], 'idx_work_date_range');
            });
        } catch (\Exception $e) {
            // Indexes might already exist
        }

        // Work Order Assignments table indexes - only adding new ones
        try {
            Schema::table('work_order_assignments', function (Blueprint $table) {
                // Index for effective date range queries (not covered by existing indexes)
                $table->index(['effective_from', 'effective_until'], 'idx_assignment_effective_range');
            });
        } catch (\Exception $e) {
            // Index might already exist
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove Work Orders table indexes
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropIndex('idx_work_tech_status');
            $table->dropIndex('idx_work_date_range');
        });

        // Remove Work Order Assignments table indexes
        Schema::table('work_order_assignments', function (Blueprint $table) {
            $table->dropIndex('idx_assignment_effective_range');
        });
    }
};
