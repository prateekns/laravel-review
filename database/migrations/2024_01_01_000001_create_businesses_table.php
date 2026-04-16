<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();

            // Business Information
            $table->string('name');
            $table->string('email')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('timezone')->default('America/New_York');

            // Address Information
            $table->text('address')->nullable();
            $table->foreignId('country_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('state_id')->nullable()->constrained()->onDelete('restrict');
            $table->foreignId('city_id')->nullable()->constrained()->onDelete('restrict');
            $table->string('street')->nullable();
            $table->string('zipcode', 20)->nullable();
            $table->string('website_url')->nullable();
            $table->string('logo')->nullable();
            $table->boolean('onboarding_completed')->default(false);
            $table->integer('num_admin')->default(0);
            $table->integer('num_technician')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->softDeletes();
            // Indexes
            $table->index(['name', 'email', 'phone']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
