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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->uuid('payment_uuid')->unique();
            $table->foreignId('business_id')->constrained('businesses')->onDelete('cascade');
            $table->foreignId('business_user_id')->constrained('business_users')->onDelete('cascade');
            $table->integer('num_admin')->default(0);
            $table->integer('num_technician')->default(0);
            $table->decimal('admin_price', 10, 2)->default(0);
            $table->decimal('technician_price', 10, 2)->default(0);
            $table->integer('proration_amt')->default(0);
            $table->decimal('total_price', 10, 2)->default(0);
            $table->enum('billing_frequency', ['monthly', 'half-yearly', 'yearly'])->default('monthly');
            $table->enum('status', ['pending', 'completed', 'failed','cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
