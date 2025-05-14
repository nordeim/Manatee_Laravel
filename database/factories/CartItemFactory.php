<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\CartManagement\Models\CartItem;
use App\Domain\UserManagement\Models\User;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CartItemFactory extends Factory
{
    protected $model = CartItem::class;

    public function definition(): array
    {
        return [
            'user_id' => null, // Can be set specifically or left for guest
            'session_id' => Str::random(40), // For guest carts
            'product_id' => Product::factory(),
            'quantity' => fake()->numberBetween(1, 5),
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
            'session_id' => null,
        ]);
    }

    public function guest(?string $sessionId = null): static
    {
         return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'session_id' => $sessionId ?? Str::random(40),
        ]);
    }
}
