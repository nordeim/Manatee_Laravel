<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Inventory\Models\InventoryMovement;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class InventoryMovementFactory extends Factory
{
    protected $model = InventoryMovement::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['sale', 'restock', 'return', 'adjustment', 'initial']);
        $quantityChange = match ($type) {
            'sale', 'adjustment' => fake()->numberBetween(-100, -1), // Adjustment can be negative
            'return', 'restock', 'initial' => fake()->numberBetween(1, 100),
            default => fake()->numberBetween(-50,50),
        };

        return [
            'product_id' => Product::factory(),
            'quantity_change' => $quantityChange,
            'type' => $type,
            'reference_id' => $type === 'sale' ? fake()->optional()->randomNumber(5) : null, // e.g. order ID
            'notes' => fake()->optional()->sentence(),
            'created_at' => fake()->dateTimeThisYear(),
        ];
    }
}
