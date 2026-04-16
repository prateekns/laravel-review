<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->string('first_name', 50);
            $table->string('last_name', 50);
            $table->string('email_1');
            $table->string('email_2')->nullable();
            $table->string('phone_1');
            $table->string('phone_2')->nullable();
            $table->text('commercial_pool_details')->nullable();
            $table->boolean('status')->default(true)->comment('1=active, 0=inactive');

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
            $table->string('pump_details', 100)->nullable();
            $table->string('pump_image')->nullable();
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
            $table->index(['business_id', 'status']);
            $table->index(['business_id', 'email_1']);
            $table->index(['business_id', 'first_name', 'last_name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
