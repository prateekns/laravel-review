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
        Schema::create('work_order_positions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->unsignedBigInteger('technician_id');
            $table->date('scheduled_date');
            $table->integer('position')->default(0);
            $table->string('instance_id')->nullable();
            $table->timestamps();

            $table->foreign('work_order_id')->references('id')->on('work_orders')->onDelete('cascade');
            $table->foreign('technician_id')->references('id')->on('technicians')->onDelete('cascade');

            // Create an index for faster lookups
            $table->index(['technician_id', 'scheduled_date']);

            // Add composite index for work order assignments
            $table->index(['work_order_id', 'technician_id'], 'idx_work_tech');

            // Add index for position sorting
            $table->index(['position'], 'idx_position');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('work_order_positions');
    }
};
