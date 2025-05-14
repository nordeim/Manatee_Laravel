<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Utility\Models\QuizResult;
use App\Domain\UserManagement\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuizResultFactory extends Factory
{
    protected $model = QuizResult::class;

    public function definition(): array
    {
        return [
            'user_id' => fake()->boolean(70) ? User::factory() : null,
            'email' => function (array $attributes) {
                return $attributes['user_id'] ? null : fake()->safeEmail;
            },
            'answers' => [
                'feeling' => fake()->randomElement(['relax', 'energize', 'focus', 'sleep']),
                'scentFamily' => fake()->randomElement(['floral', 'citrus', 'woody', 'herbal']),
                'format' => fake()->randomElement(['oil', 'soap', 'both']),
            ],
            'recommendations' => [fake()->numberBetween(1,10), fake()->numberBetween(1,10)], // Example: array of product IDs
            'created_at' => fake()->dateTimeThisYear(),
        ];
    }
}
