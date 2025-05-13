<?php

declare(strict_types=1);

namespace App\Domain\Inventory\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Catalog\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // No updated_at column

    protected $fillable = [
        'product_id',
        'quantity_change',
        'type',
        'reference_id',
        'notes',
        'created_at', // Allow mass assignment if setting manually
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'created_at' => 'datetime',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
