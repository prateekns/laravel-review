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
        Schema::create('work_orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('business_id');
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('template_id')->nullable();
            $table->unsignedBigInteger('technician_id')->nullable();
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('additional_task', 255)->nullable();
            $table->date('preferred_start_date');
            $table->time('preferred_start_time');
            $table->boolean('communication_notes')->default(true);
            $table->boolean('is_recurring')->default(false);
            $table->string('photo')->nullable();
            $table->unsignedTinyInteger('status')->default(1)->comment('1=pending, 2=scheduled, 3=in_progress, 4=completed, 5=cancelled');
            $table->text('extra_work_done')->nullable()->comment('Additional manual work entries made by the technician');

            $table->timestamps();
            $table->softDeletes();

            // Indexes for better performance
            $table->index(['business_id', 'customer_id']);
            $table->index('status');
            $table->index('template_id');

            // Foreign key constraints
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('set null');
            $table->foreign('technician_id')->references('id')->on('technicians')->onDelete('set null');
        });

        Schema::create('work_order_checklist_items', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('work_order_id');
            $table->unsignedBigInteger('business_id');
            $table->string('description', 255);
            $table->boolean('is_completed')->default(false);
            $table->timestamps();

            $table->foreign('work_order_id')->references('id')->on('work_orders')->onDelete('cascade');
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_checklist_items');
        Schema::dropIfExists('work_orders');
    }
};
