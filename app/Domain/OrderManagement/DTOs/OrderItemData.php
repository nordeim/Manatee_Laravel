<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class OrderItemData extends Data
{
    public function __construct(
        public int $product_id,
        public string $product_name_snapshot,
        public int $quantity,
        public int $unit_price_minor_amount,
        public int $total_minor_amount,
        public string $currency_code,
        public string|Optional|null $product_sku_snapshot,
        public array|Optional|null $product_options_snapshot
    ) {}
}
