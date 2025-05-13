<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->integer('quantity_change')->comment('Positive for stock in, negative for stock out');
            $table->enum('type', ['sale', 'restock', 'return', 'adjustment', 'initial']);
            $table->unsignedBigInteger('reference_id')->nullable()->comment('e.g., order_id for sale, purchase_order_id for restock');
            $table->text('notes')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent(); // No updated_at for immutable log

            $table->index(['product_id', 'created_at']);
            $table->index('type');
            $table->index('reference_id'); // If you query by reference
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
