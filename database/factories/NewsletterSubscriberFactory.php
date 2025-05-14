<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\Utility\Models\NewsletterSubscriber;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class NewsletterSubscriberFactory extends Factory
{
    protected $model = NewsletterSubscriber::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'subscribed_at' => now(), // Or fake()->dateTimeThisYear()
            'unsubscribed_at' => null,
            'token' => Str::random(60),
        ];
    }

    public function unsubscribed(): static
    {
        return $this->state(fn (array $attributes) => [
            'unsubscribed_at' => now(),
        ]);
    }
}
