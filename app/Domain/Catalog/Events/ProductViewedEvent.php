<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Events;

use App\Domain\Catalog\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductViewedEvent
{
    use Dispatchable, SerializesModels;

    public Product $product;
    public ?int $userId; // Optional user ID if logged in

    public function __construct(Product $product, ?int $userId = null)
    {
        $this->product = $product;
        $this->userId = $userId;
    }
}
