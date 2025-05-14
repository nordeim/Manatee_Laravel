<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->string('session_id', 191)->nullable()->comment('Used for guest carts');
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            // $table->foreignId('product_variant_id')->nullable()->constrained('product_variants')->cascadeOnDelete(); // If using variants
            $table->integer('quantity')->unsigned();
            $table->timestamps();

            // Ensure a product can only appear once per user cart or per session cart
            $table->unique(['user_id', 'product_id'/*, 'product_variant_id'*/], 'uq_cart_item_user_product');
            $table->unique(['session_id', 'product_id'/*, 'product_variant_id'*/], 'uq_cart_item_session_product');
            $table->index('session_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
    }
};
