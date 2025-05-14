<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Utility\Models\EmailLog;
use App\Domain\UserManagement\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmailLogFactory extends Factory
{
    protected $model = EmailLog::class;

    public function definition(): array
    {
        return [
            'user_id' => fake()->boolean(80) ? User::factory() : null,
            'to_email' => fake()->safeEmail(),
            'subject' => fake()->sentence(4),
            'email_type' => fake()->randomElement(['welcome', 'password_reset', 'order_confirmation', 'newsletter']),
            'status' => fake()->randomElement(['sent','failed','pending']),
            'mailer_error' => function (array $attributes) {
                return $attributes['status'] === 'failed' ? fake()->sentence() : null;
            },
            'sent_at' => function (array $attributes) {
                return $attributes['status'] === 'sent' ? fake()->dateTimeThisMonth() : null;
            },
            'created_at' => fake()->dateTimeThisMonth(),
        ];
    }
}
