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
            // Add recurring fields
            $table->enum('frequency', ['daily', 'weekly', 'semi_monthly', 'monthly'])->nullable()->after('is_recurring');
            $table->unsignedTinyInteger('repeat_after')->default(1)->after('frequency')->comment('1=1 week, 2=2 weeks, 3=3 weeks, 4=4 weeks for weekly; 1=1 month, 2=2 months, etc. for monthly');
            $table->json('selected_days')->nullable()->after('repeat_after')->comment('Array of selected days for weekly/semi-monthly frequency');
            $table->date('end_date')->nullable()->after('selected_days')->comment('End date for recurring services');
            $table->enum('monthly_day_type', ['first', 'second', 'third', 'fourth'])->nullable()->after('end_date')->comment('For monthly frequency: first, second, third, fourth');
            $table->enum('monthly_day_of_week', ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'])->nullable()->after('monthly_day_type')->comment('For monthly frequency: day of week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('work_orders', function (Blueprint $table) {
            $table->dropColumn([
                'frequency',
                'repeat_after',
                'selected_days',
                'end_date',
                'monthly_day_type',
                'monthly_day_of_week'
            ]);
        });
    }
};
