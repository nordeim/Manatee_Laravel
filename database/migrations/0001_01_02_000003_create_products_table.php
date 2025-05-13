<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name', 150);
            $table->string('slug', 170)->unique();
            $table->text('description')->nullable();
            $table->text('short_description')->nullable()->comment('Brief description for listings/previews');
            $table->string('image', 191)->nullable()->comment('Path to main image');
            $table->json('gallery_images')->nullable()->comment('JSON array of additional image paths');
            $table->integer('price_minor_amount')->unsigned()->comment('Price in minor units (e.g., cents)');
            $table->string('currency_code', 3)->default('USD');
            $table->json('benefits')->nullable()->comment('Product benefits, stored as JSON array of strings');
            $table->text('ingredients')->nullable()->comment('List of key ingredients');
            $table->text('usage_instructions')->nullable()->comment('How to use the product');
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->integer('stock_quantity')->default(0);
            $table->integer('low_stock_threshold')->unsigned()->default(20);
            $table->integer('reorder_point')->unsigned()->default(30);
            $table->boolean('backorder_allowed')->default(false)->comment('Allow purchase when stock_quantity <= 0');
            $table->string('highlight_text', 50)->nullable();
            $table->string('size', 50)->nullable()->comment('e.g., 10ml, 100g');
            $table->string('scent_profile', 191)->nullable();
            $table->string('origin', 100)->nullable();
            $table->string('sku', 100)->nullable()->unique();
            $table->timestamps();

            $table->index(['is_active', 'is_featured']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
