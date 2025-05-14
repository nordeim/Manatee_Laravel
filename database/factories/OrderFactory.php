<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Domain\OrderManagement\Models\Order;
use App\Domain\UserManagement\Models\User;
// use App\Domain\UserManagement\Models\Address;
use App\Domain\Promotion\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $subtotal = fake()->numberBetween(2000, 20000); // 20.00 - 200.00
        $shipping = fake()->numberBetween(500, 2000);
        $tax = (int)($subtotal * 0.07); // Example 7% tax
        $total = $subtotal + $shipping + $tax;

        return [
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'user_id' => User::factory(),
            'guest_email' => null, // Or fake()->safeEmail if user_id is null
            // 'shipping_address_id' => Address::factory()->shipping(), // If using Address model
            // 'billing_address_id' => Address::factory()->billing(),  // If using Address model
            'shipping_name' => fake()->name(),
            'shipping_email' => fake()->safeEmail(),
            'shipping_address' => fake()->streetAddress(),
            'shipping_city' => fake()->city(),
            'shipping_state' => fake()->stateAbbr(),
            'shipping_zip' => fake()->postcode(),
            'shipping_country' => fake()->countryCode(),

            'status' => fake()->randomElement(['pending_payment','paid','processing','shipped','delivered','completed']),
            'payment_status' => 'paid',
            'payment_intent_id' => 'pi_' . Str::random(24),
            'payment_gateway' => 'stripe',
            'subtotal_minor_amount' => $subtotal,
            'discount_minor_amount' => 0,
            // 'coupon_id' => fake()->optional()->randomElement(Coupon::pluck('id')->toArray()),
            'shipping_cost_minor_amount' => $shipping,
            'tax_minor_amount' => $tax,
            'total_minor_amount' => $total,
            'currency_code' => 'USD',
            'order_notes' => fake()->optional()->sentence(),
            'paid_at' => now(),
            'created_at' => fake()->dateTimeThisYear(),
            'updated_at' => now(),
        ];
    }

    public function guest(): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => null,
            'guest_email' => fake()->safeEmail(),
        ]);
    }
}
