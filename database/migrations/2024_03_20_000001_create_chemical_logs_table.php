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
        Schema::create('chemical_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('work_order_id');
            $table->unsignedBigInteger('instance_id')->nullable();
            $table->unsignedBigInteger('chemical_id');
            $table->string('chemical_name')->nullable();
            $table->decimal('reading', 8, 2)->nullable();
            $table->string('range')->nullable();
            $table->decimal('ideal_target', 8, 2)->nullable();
            $table->string('unit')->nullable();
            $table->string('chemical_used')->nullable();
            $table->decimal('qty_added', 8, 2)->nullable();
            $table->string('chemical_used_unit')->nullable();
            $table->integer('tabs')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('work_order_id')->references('id')->on('work_orders')->onDelete('cascade');
            $table->foreign('chemical_id')->references('id')->on('chemicals')->onDelete('cascade');

            // Indexes for better performance
            $table->index('work_order_id');
            $table->index('chemical_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chemicals_log');
    }
};
