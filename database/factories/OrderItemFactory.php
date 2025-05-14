<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\OrderManagement\Models\OrderItem;
use App\Domain\OrderManagement\Models\Order;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $product = Product::factory()->create(); // Create a product for this item
        $quantity = fake()->numberBetween(1, 3);
        $unitPrice = $product->price_minor_amount; // Use product's actual price

        return [
            'order_id' => Order::factory(),
            'product_id' => $product->id,
            'product_name_snapshot' => $product->name,
            'product_sku_snapshot' => $product->sku,
            'product_options_snapshot' => ['size' => $product->size, 'scent_profile' => $product->scent_profile],
            'quantity' => $quantity,
            'unit_price_minor_amount' => $unitPrice,
            'total_minor_amount' => $quantity * $unitPrice,
            'currency_code' => $product->currency_code,
        ];
    }
}
