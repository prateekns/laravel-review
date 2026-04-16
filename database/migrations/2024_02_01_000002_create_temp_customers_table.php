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
        Schema::create('temp_customers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->cascadeOnDelete();

            // Basic customer information
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email_1')->nullable();
            $table->string('email_2')->nullable();
            $table->string('phone_1')->nullable();
            $table->string('phone_2')->nullable();
            $table->boolean('status')->default(true);

            // Address information
            $table->string('address')->nullable();
            $table->string('street')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();

            // Pool information
            $table->text('commercial_pool_details')->nullable();
            $table->double('pool_size_gallons', 10, 2)->nullable();
            $table->double('pool_length', 10, 2)->nullable();
            $table->double('pool_width', 10, 2)->nullable();
            $table->double('pool_depth', 10, 2)->nullable();

            // Pool equipment details
            $table->string('filter_details')->nullable();
            $table->string('pump_details')->nullable();
            $table->string('cleaner_details')->nullable();
            $table->string('heater_details')->nullable();
            $table->string('heat_pump_details')->nullable();
            $table->string('aux_details')->nullable();
            $table->string('aux2_details')->nullable();
            $table->string('salt_system_details')->nullable();

            // Notes
            $table->text('entry_instruction')->nullable();
            $table->text('tech_notes')->nullable();
            $table->text('admin_notes')->nullable();

            // Import tracking
            $table->string('import_batch')->index();
            $table->boolean('is_processed')->default(false);
            $table->text('validation_errors')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temp_customers');
    }
};
