<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Catalog\Models\ProductAttribute;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductAttributeFactory extends Factory
{
    protected $model = ProductAttribute::class;

    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'scent_type' => fake()->randomElement(['floral','woody','citrus','oriental','fresh', 'herbal', 'spicy', 'sweet', null]),
            'mood_effect' => fake()->randomElement(['calming','energizing','focusing','balancing', 'uplifting', 'grounding', null]),
            'intensity_level' => fake()->randomElement(['light','medium','strong', null]),
        ];
    }
}
