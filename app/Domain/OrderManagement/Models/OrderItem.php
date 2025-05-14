<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Catalog\Models\Product;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 'product_id',
        'product_name_snapshot', 'product_sku_snapshot', 'product_options_snapshot',
        'quantity', 'unit_price_minor_amount', 'total_minor_amount', 'currency_code',
    ];

    protected $casts = [
        'product_options_snapshot' => 'array',
        'quantity' => 'integer',
        'unit_price_minor_amount' => 'integer',
        'total_minor_amount' => 'integer',
    ];

    // Virtual attributes for MonetaryAmount objects
    protected function unitPrice(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['unit_price_minor_amount'], $attributes['currency_code'])
        );
    }

    protected function total(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['total_minor_amount'], $attributes['currency_code'])
        );
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
