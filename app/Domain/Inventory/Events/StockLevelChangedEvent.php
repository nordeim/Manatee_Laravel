<?php

declare(strict_types=1);

namespace App\Domain\Inventory\Events;

use App\Domain\Catalog\Models\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class StockLevelChangedEvent
{
    use Dispatchable, SerializesModels;

    public Product $product;
    public int $quantityChange;
    public string $type;
    public ?int $referenceId;

    public function __construct(Product $product, int $quantityChange, string $type, ?int $referenceId = null)
    {
        $this->product = $product;
        $this->quantityChange = $quantityChange;
        $this->type = $type;
        $this->referenceId = $referenceId;
    }
}
