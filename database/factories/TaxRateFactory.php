<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Utility\Models\TaxRate;
use App\Domain\UserManagement\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaxRateFactory extends Factory
{
    protected $model = TaxRate::class;

    public function definition(): array
    {
        return [
            'name' => fake()->city() . ' Sales Tax',
            'country_code' => fake()->countryCode(),
            'state_code' => fake()->optional(0.7)->stateAbbr(),
            'postal_code_pattern' => null,
            'city' => null,
            'rate_percentage' => fake()->randomFloat(4, 0, 20), // e.g., 7.2500
            'is_compound' => fake()->boolean(10),
            'priority' => fake()->numberBetween(1, 3),
            'is_active' => true,
            'start_date' => fake()->optional()->date(),
            'end_date' => null,
            'created_by_user_id' => User::factory()->admin(),
        ];
    }
}
