<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\UserManagement\Models\Address;
use App\Domain\UserManagement\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AddressFactory extends Factory
{
    protected $model = Address::class;

    public function definition(): array
    {
        return [
            'user_id' => User::factory(), // Or null if allowing guest addresses
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'company' => fake()->optional()->company(),
            'address_line1' => fake()->streetAddress(),
            'address_line2' => fake()->optional()->secondaryAddress(),
            'city' => fake()->city(),
            'state' => fake()->stateAbbr(),
            'postal_code' => fake()->postcode(),
            'country_code' => fake()->countryCode(), // e.g., 'US'
            'phone' => fake()->optional()->phoneNumber(),
            'type' => fake()->randomElement(['billing', 'shipping', 'general', null]),
            'is_default_shipping' => false,
            'is_default_billing' => false,
        ];
    }

    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function shipping(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'shipping',
        ]);
    }

    public function billing(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'billing',
        ]);
    }

    public function defaultShipping(): static
    {
        return $this->shipping()->state(fn (array $attributes) => [
            'is_default_shipping' => true,
        ]);
    }

    public function defaultBilling(): static
    {
        return $this->billing()->state(fn (array $attributes) => [
            'is_default_billing' => true,
        ]);
    }
}
