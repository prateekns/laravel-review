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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('business_id')->constrained('businesses')->index();
            $table->string('invoice_id')->unique()->index(); // Stripe invoice ID
            $table->string('invoice_number')->nullable()->index();
            $table->string('customer_id')->index(); // Stripe customer ID
            $table->string('subscription_id')->nullable()->index(); // References subscriptions.stripe_id
            $table->decimal('amount_due', 10, 2)->nullable();
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->text('description')->nullable();
            $table->string('billing_reason')->nullable();
            $table->string('currency', 3)->default('usd');
            $table->string('invoice_status');
            $table->string('invoice_url')->nullable();
            $table->string('invoice_pdf')->nullable();
            $table->datetime('period_start')->nullable();
            $table->datetime('period_end')->nullable();
            $table->foreignId('order_id')->index();
            $table->foreign('order_id', 'invoices_order_id_fk')->references('id')->on('orders');
            $table->timestamp('created')->nullable();
            $table->timestamps();

            // Foreign key constraint
            // $table->foreign('subscription_id')
            //       ->references('stripe_id')
            //       ->on('subscriptions')
            //       ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
