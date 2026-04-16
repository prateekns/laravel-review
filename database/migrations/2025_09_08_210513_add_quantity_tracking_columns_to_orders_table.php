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
        Schema::table('orders', function (Blueprint $table) {
            $table->integer('admin_qty_change')->default(0)->after('num_technician');
            $table->integer('technician_qty_change')->default(0)->after('admin_qty_change');
            $table->integer('total_admin')->default(0)->after('technician_qty_change');
            $table->integer('total_technician')->default(0)->after('total_admin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'admin_qty_change',
                'technician_qty_change',
                'total_admin',
                'total_technician'
            ]);
        });
    }
};
