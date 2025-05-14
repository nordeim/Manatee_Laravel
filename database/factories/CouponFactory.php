<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Promotion\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CouponFactory extends Factory
{
    protected $model = Coupon::class;

    public function definition(): array
    {
        $type = fake()->randomElement(['percentage', 'fixed_amount']);
        $value = ($type === 'percentage') ? fake()->numberBetween(5, 50) : fake()->numberBetween(500, 5000); // 5-50% or $5-$50

        return [
            'code' => strtoupper(Str::random(8)),
            'description' => fake()->sentence,
            'type' => $type,
            'value_minor_amount' => $value,
            'currency_code' => ($type === 'fixed_amount') ? 'USD' : null,
            'max_uses' => fake()->optional(0.7, 1000)->numberBetween(100, 1000),
            'uses_count' => 0,
            'max_uses_per_user' => fake()->optional(0.5, 1)->numberBetween(1, 5),
            'min_purchase_minor_amount' => fake()->optional(0.6, 2000)->numberBetween(2000, 10000), // $20-$100
            'min_purchase_currency_code' => 'USD',
            'valid_from' => fake()->optional(0.8)->dateTimeBetween('-1 month', '+1 week'),
            'valid_to' => fake()->optional(0.8)->dateTimeBetween('+1 month', '+6 months'),
            'is_active' => true,
        ];
    }
}
