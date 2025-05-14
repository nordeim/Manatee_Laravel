<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 32)->unique()->comment('User-friendly order identifier');
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('guest_email', 191)->nullable();

            // Storing denormalized address details directly on order as per sample schema
            // Or using foreign keys to a shared 'addresses' table if preferred (ERD indicated this)
            // The sample schema has 'shipping_name', 'shipping_email', 'shipping_address' etc directly on orders table.
            // Let's follow the sample schema here for directness, though an Address model is cleaner.
            // If using an Address model for orders:
            // $table->foreignId('shipping_address_id')->nullable()->constrained('addresses')->nullOnDelete();
            // $table->foreignId('billing_address_id')->nullable()->constrained('addresses')->nullOnDelete();

            // Following sample schema's direct fields for order addresses:
            $table->string('shipping_name', 191)->nullable();
            $table->string('shipping_email', 191)->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('shipping_address_line2', 191)->nullable();
            $table->string('shipping_city', 100)->nullable();
            $table->string('shipping_state', 100)->nullable();
            $table->string('shipping_zip', 20)->nullable();
            $table->string('shipping_country', 50)->nullable();

            // If billing address can be different and is also stored directly:
            // $table->string('billing_name', 191)->nullable(); ... etc.
            // For simplicity, assuming shipping address can double as billing or billing is tied to user.

            $table->enum('status', ['pending_payment','paid','processing','shipped','delivered','cancelled','refunded','disputed','payment_failed','completed'])->default('pending_payment');
            $table->string('payment_status', 50)->default('pending');
            $table->string('payment_intent_id', 191)->nullable()->index();
            $table->string('payment_gateway', 50)->nullable();

            $table->integer('subtotal_minor_amount')->unsigned()->default(0);
            $table->integer('discount_minor_amount')->unsigned()->default(0);
            $table->foreignId('coupon_id')->nullable()->constrained('coupons')->nullOnDelete();
            $table->string('coupon_code_applied', 50)->nullable(); // Denormalized for easy display
            $table->integer('shipping_cost_minor_amount')->unsigned()->default(0);
            $table->integer('tax_minor_amount')->unsigned()->default(0);
            $table->integer('total_minor_amount')->unsigned()->default(0);
            $table->string('currency_code', 3)->default('USD');

            $table->text('order_notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('dispute_id', 191)->nullable();
            $table->timestamp('disputed_at')->nullable();
            $table->string('refund_id', 191)->nullable();
            $table->timestamp('refunded_at')->nullable();
            $table->string('tracking_number', 100)->nullable();
            $table->string('carrier', 100)->nullable();
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
