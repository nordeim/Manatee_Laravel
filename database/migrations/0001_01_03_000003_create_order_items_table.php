<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete(); // Or nullOnDelete if product can be deleted but history kept
            // $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->nullOnDelete(); // If using variants

            $table->string('product_name_snapshot', 150)->comment('Product name at time of order');
            $table->string('product_sku_snapshot', 100)->nullable()->comment('Product SKU at time of order');
            $table->json('product_options_snapshot')->nullable()->comment('e.g., size, color at time of order');

            $table->integer('quantity')->unsigned();
            $table->integer('unit_price_minor_amount')->unsigned()->comment('Price per unit in minor units at time of order');
            $table->integer('total_minor_amount')->unsigned()->comment('Total price for this item (quantity * unit_price)');
            $table->string('currency_code', 3)->default('USD');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
