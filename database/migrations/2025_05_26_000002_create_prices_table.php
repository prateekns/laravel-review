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
        Schema::create('prices', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('type', ['admin', 'technician'])->default('admin');
            $table->string('label')->nullable();
            $table->string('description')->nullable();
            $table->string('price_id')->unique();
            $table->enum('interval', ['daily', 'monthly', 'half-yearly', 'yearly'])->default('monthly');
            $table->decimal('price', 10, 2);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
