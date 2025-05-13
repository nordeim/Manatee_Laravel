<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Catalog\Models\Product;
use App\Domain\Catalog\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(rand(2, 4), true);
        return [
            'name' => Str::title($name),
            'slug' => Str::slug($name) . '-' . Str::random(4), // Ensure uniqueness
            'description' => fake()->paragraphs(3, true),
            'short_description' => fake()->sentence(15),
            // 'image' => 'products/sample-' . rand(1, 10) . '.jpg', // Placeholder for image path
            'price_minor_amount' => fake()->numberBetween(1000, 10000), // e.g., $10.00 to $100.00
            'currency_code' => 'USD',
            'category_id' => Category::factory(),
            'is_featured' => fake()->boolean(20),
            'is_active' => true,
            'stock_quantity' => fake()->numberBetween(0, 200),
            'low_stock_threshold' => 20,
            'reorder_point' => 30,
            'backorder_allowed' => fake()->boolean(10),
            'sku' => strtoupper(Str::random(3) . '-' . fake()->randomNumber(5, true)),
            'size' => fake()->randomElement(['10ml', '30ml', '50ml', '100g', '200g', null]),
            'scent_profile' => fake()->words(rand(2,3), true),
            'origin' => fake()->optional()->country(),
        ];
    }
}
