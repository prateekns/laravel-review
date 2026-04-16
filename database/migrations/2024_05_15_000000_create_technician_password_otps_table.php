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
        Schema::create('technician_password_otps', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id');
            $table->foreign('staff_id')->references('staff_id')->on('technicians')->onDelete('cascade');
            $table->string('otp', 6);
            $table->boolean('is_verified')->default(false);
            $table->timestamp('expires_at');
            $table->timestamps();

            $table->index('staff_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technician_password_otps');
    }
};
