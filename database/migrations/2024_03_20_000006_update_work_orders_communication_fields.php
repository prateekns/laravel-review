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
            // Add new technician_customer_coordination column
            $table->boolean('technician_customer_coordination')->default(false)
                ->comment('Indicates whether technician is allowed to coordinate directly with the customer');

            // Change communication_notes to text type
            $table->text('communication_notes')->nullable()->change()
                ->comment('Information shared by technician to customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn('technician_customer_coordination');
            $table->boolean('communication_notes')->nullable()->change();
        });
    }
};
