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
        Schema::table('work_order_assignments', function (Blueprint $table) {
            // Basic information
            $table->string('type')->nullable()->after('id');
            $table->unsignedBigInteger('business_id')->nullable()->after('instance_id');
            $table->unsignedBigInteger('customer_id')->nullable()->after('business_id');
            $table->unsignedBigInteger('template_id')->nullable()->after('customer_id');
            $table->string('name', 100)->nullable()->after('template_id');
            $table->text('description')->nullable()->after('name');
            $table->string('additional_task', 255)->nullable()->after('description');

            // Scheduling information
            $table->date('preferred_start_date')->nullable()->after('additional_task');
            $table->time('preferred_start_time')->nullable()->after('preferred_start_date');

            // Communication and coordination
            $table->text('communication_notes')->nullable()->after('preferred_start_time')
                ->comment('Information shared by technician to customer');
            $table->boolean('technician_customer_coordination')->default(false)->after('communication_notes')
                ->comment('Indicates whether technician is allowed to coordinate directly with the customer');

            // Recurrence settings
            $table->boolean('is_recurring')->default(false)->after('technician_customer_coordination');
            $table->string('frequency')->nullable()->after('is_recurring')
                ->comment('daily, weekly, monthly, custom');
            $table->integer('repeat_after')->nullable()->after('frequency')
                ->comment('Number of days/weeks/months to repeat after');
            $table->json('selected_days')->nullable()->after('repeat_after')
                ->comment('Selected days for weekly/monthly recurrence');
            $table->date('end_date')->nullable()->after('selected_days');
            $table->string('monthly_day_type')->nullable()->after('end_date')
                ->comment('specific_date, first, second, third, fourth, last');
            $table->string('monthly_day_of_week')->nullable()->after('monthly_day_type')
                ->comment('monday, tuesday, wednesday, thursday, friday, saturday, sunday');

            // Additional fields
            $table->string('photo')->nullable()->after('monthly_day_of_week');
            $table->text('extra_work_done')->nullable()->after('photo')
                ->comment('Additional manual work entries made by the technician');

            // Foreign key constraints
            $table->foreign('business_id')->references('id')->on('businesses')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('set null');
            $table->foreign('template_id')->references('id')->on('templates')->onDelete('set null');

            // Indexes for better performance
            $table->index(['business_id', 'customer_id']);
            $table->index('template_id');
            $table->index('type');
            $table->index('is_recurring');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_order_assignments', function (Blueprint $table) {
            // Drop indexes
            $table->dropIndex(['business_id', 'customer_id']);
            $table->dropIndex(['template_id']);
            $table->dropIndex(['type']);
            $table->dropIndex(['is_recurring']);

            // Drop foreign keys
            $table->dropForeign(['business_id']);
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['template_id']);

            // Drop columns
            $table->dropColumn([
                'type',
                'business_id',
                'customer_id',
                'template_id',
                'name',
                'description',
                'additional_task',
                'preferred_start_date',
                'preferred_start_time',
                'communication_notes',
                'technical_customer_coordination',
                'is_recurring',
                'frequency',
                'repeat_after',
                'selected_days',
                'end_date',
                'monthly_day_type',
                'monthly_day_of_week',
                'photo',
                'extra_work_done'
            ]);
        });
    }
};
