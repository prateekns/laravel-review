<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('completed_job_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->cascadeOnDelete();
            $table->bigInteger('instance_id')->nullable();
            $table->foreignId('customer_id')->constrained('customers')->nullable()->cascadeOnDelete();
            $table->string('name', 100)->nullable();
            $table->string('email_1');
            $table->string('email_2')->nullable();
            $table->string('phone_1');
            $table->string('phone_2')->nullable();
            $table->string('pool_type', 100)->nullable();
            $table->text('commercial_pool_details')->nullable();

            // Address
            $table->string('address');
            $table->string('street')->nullable();
            $table->string('zip_code', 20);
            $table->string('city');
            $table->string('state');
            $table->string('country');

            // Pool Details
            $table->double('pool_size_gallons', 10, 2)->nullable();
            $table->double('pool_length', 10, 2)->nullable();
            $table->double('pool_width', 10, 2)->nullable();
            $table->double('pool_depth', 10, 2)->nullable();

            // Equipment Details
            $table->string('clean_psi', 100)->nullable();
            $table->string('clean_psi_image')->nullable();
            $table->string('pump_details', 100)->nullable();
            $table->string('pump_image')->nullable();
            $table->string('filter_details', 100)->nullable();
            $table->string('filter_image')->nullable();
            $table->string('cleaner_details', 100)->nullable();
            $table->string('cleaner_image')->nullable();
            $table->string('heat_pump_details', 100)->nullable();
            $table->string('heat_pump_image')->nullable();
            $table->string('aux_details', 100)->nullable();
            $table->string('aux_image')->nullable();
            $table->string('aux2_details', 100)->nullable();
            $table->string('aux2_image')->nullable();
            $table->string('heater_details', 100)->nullable();
            $table->string('heater_image')->nullable();
            $table->string('salt_system_details', 100)->nullable();
            $table->string('salt_system_image')->nullable();

            // Notes
            $table->text('entry_instruction')->nullable();
            $table->text('tech_notes')->nullable();
            $table->text('admin_notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Add indexes for better performance
            $table->index('work_order_id');
            $table->index('customer_id');
            $table->index(['work_order_id', 'email_1']);
            $table->index(['work_order_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('completed_job_customers');
    }
};
