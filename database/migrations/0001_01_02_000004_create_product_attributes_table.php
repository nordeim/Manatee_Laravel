<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_attributes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->enum('scent_type', ['floral','woody','citrus','oriental','fresh', 'herbal', 'spicy', 'sweet'])->nullable();
            $table->enum('mood_effect', ['calming','energizing','focusing','balancing', 'uplifting', 'grounding'])->nullable();
            $table->enum('intensity_level', ['light','medium','strong'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_attributes');
    }
};
