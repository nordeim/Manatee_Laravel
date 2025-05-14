<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;
use App\Domain\UserManagement\Models\User; // For typing
use App\Domain\OrderManagement\DTOs\OrderItemData; // Nested DTO
use Spatie\LaravelData\Attributes\DataCollectionOf;

class OrderData extends Data
{
    public function __construct(
        public string $order_number,
        public string $currency_code,
        public int $total_minor_amount,
        public int $subtotal_minor_amount,
        public string $status,
        public User|Optional|null $user, // If associated with a registered user
        public string|Optional|null $guest_email,
        #[DataCollectionOf(OrderItemData::class)]
        public array|Optional $items, // Array of OrderItemData
        public string|Optional|null $payment_intent_id,
        public string|Optional|null $payment_gateway,
        // ... include other relevant fields from the Order model for creation/update
        public int|Optional $discount_minor_amount,
        public int|Optional $shipping_cost_minor_amount,
        public int|Optional $tax_minor_amount,
        public string|Optional|null $coupon_code_applied,
        public string|Optional|null $shipping_name,
        // ... all shipping address fields
        // ... order notes, tracking etc.
    ) {}
}
