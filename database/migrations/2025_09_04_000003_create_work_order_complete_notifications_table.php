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
        Schema::create('work_order_complete_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')
                ->constrained('work_orders')
                ->onDelete('cascade');
            $table->string('instance_id')->nullable();
            $table->string('business_id')->nullable();
            $table->foreignId('technician_id')
                ->constrained('technicians')
                ->onDelete('cascade');

            $table->text('customer_message')->nullable();
            $table->string('customer_image_1')->nullable();
            $table->string('customer_image_2')->nullable();
            $table->text('business_message')->nullable();
            $table->string('business_image_1')->nullable();
            $table->string('business_image_2')->nullable();
            $table->timestamps();

            // Index for faster lookups
            $table->index(['work_order_id', 'technician_id'], 'work_order_technician_idx');
            $table->index('instance_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_complete_notifications');
    }
};
