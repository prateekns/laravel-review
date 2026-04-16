<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up()
    {
        Schema::create('work_order_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained('work_orders')->onDelete('cascade');
            $table->foreignId('technician_id')->nullable();
            $table->foreign('technician_id')->references('id')->on('technicians')->onDelete('set null');
            $table->string('instance_id')->nullable();
            $table->string('job_id')->nullable();
            $table->date('scheduled_date');
            $table->time('scheduled_time');
            $table->string('status');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_exception')->default(false);
            $table->json('recurrence_rule')->nullable();
            $table->date('effective_from')->nullable();
            $table->date('effective_until')->nullable();
            $table->timestamps();

            $table->unique(['work_order_id', 'instance_id'], 'unique_work_order_instance');
            // Performance indexes
            $table->index(['technician_id', 'scheduled_date'], 'woa_technician_scheduled_date_idx');
            $table->index(['job_id'], 'woa_job_id_idx');
            $table->index(['work_order_id', 'effective_from'], 'woa_work_effective_from_idx');
            $table->index(['effective_until'], 'woa_effective_until_idx');
        });
    }

    public function down()
    {
        Schema::dropIfExists('work_order_assignments');
    }
};
