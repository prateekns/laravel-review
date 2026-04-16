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
            // Add status field if it doesn't exist
            if (!Schema::hasColumn('customers', 'status')) {
                $table->boolean('status')->default(true)->comment('1=active, 0=inactive')->after('commercial_pool_details');
            }

            // Add indexes if they don't exist
            if (!Schema::hasIndex('customers', 'customers_business_id_status_index')) {
                $table->index(['business_id', 'status']);
            }
            if (!Schema::hasIndex('customers', 'customers_business_id_email_1_index')) {
                $table->index(['business_id', 'email_1']);
            }
            if (!Schema::hasIndex('customers', 'customers_business_id_first_name_last_name_index')) {
                $table->index(['business_id', 'first_name', 'last_name']);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Remove indexes first
            $table->dropIndex(['business_id', 'status']);
            $table->dropIndex(['business_id', 'email_1']);
            $table->dropIndex(['business_id', 'first_name', 'last_name']);

            // Then remove the column
            $table->dropColumn('status');
        });
    }
};
