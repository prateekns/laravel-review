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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            // Application settings
            $table->string('currency', 3)->default('USD');
            $table->string('training_video')->nullable();
            $table->integer('trial_period')->default(14)->comment('Trial Days');
            $table->integer('trial_admin')->default(1)->comment('Number of Trial Admin');
            $table->integer('trial_technician')->default(2)->comment('Number of Trial Technician');
            $table->string('admin_product_id')->nullable()->comment('Admin Stripe Product Id');
            $table->string('technician_product_id')->nullable()->comment('Technician Stripe Product Id');
            $table->decimal('discount_half_yearly', 10, 2)->nullable()->comment('Half-Yearly % Discount');
            $table->decimal('discount_yearly', 10, 2)->nullable()->comment('Yearly % Discount');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
