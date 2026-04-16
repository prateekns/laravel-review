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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('admin_email')->nullable()->after('status');
            $table->string('info_email')->nullable()->after('admin_email');
            $table->string('website')->nullable()->after('admin_email');
            $table->string('phone')->nullable()->after('website');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'admin_email',
                'info_email',
                'website',
                'phone'
            ]);
        });
    }
};
