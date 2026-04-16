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
        Schema::create('used_maintenance_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')
                ->constrained('work_orders')
                ->onDelete('cascade');
            $table->string('instance_id')->nullable();
            $table->string('item');
            $table->decimal('quantity', 10, 2);
            $table->string('unit');
            $table->timestamps();
            $table->softDeletes();

            // Add index for better query performance
            $table->index(['work_order_id', 'instance_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('used_maintenance_items');
    }
};
