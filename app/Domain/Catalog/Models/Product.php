<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use App\Casts\MonetaryAmountCast; // This was the plan for the cast
use App\Domain\Catalog\Models\Category;
use App\Domain\Catalog\Models\ProductAttribute;
use App\Domain\Inventory\Models\InventoryMovement;
use App\Domain\OrderManagement\Models\OrderItem;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute; // Eloquent's Attribute class

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'slug', 'description', 'short_description', 'image', 'gallery_images',
        'price_minor_amount', 'currency_code', // Storing these directly
        'benefits', 'ingredients', 'usage_instructions', 'category_id',
        'is_featured', 'is_active', 'stock_quantity', 'low_stock_threshold',
        'reorder_point', 'backorder_allowed', 'highlight_text', 'size',
        'scent_profile', 'origin', 'sku',
    ];

    protected $casts = [
        'gallery_images' => 'array',
        'benefits' => 'array',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'backorder_allowed' => 'boolean',
        'price_minor_amount' => 'integer',
        'stock_quantity' => 'integer',
        'low_stock_threshold' => 'integer',
        'reorder_point' => 'integer',
    ];

    // Virtual 'price' attribute using MonetaryAmount
    protected function price(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount(
                (int) ($attributes['price_minor_amount'] ?? 0),
                (string) ($attributes['currency_code'] ?? config('thescent.default_currency', 'USD'))
            ),
            set: fn (MonetaryAmount $value) => [
                'price_minor_amount' => $value->getMinorAmount(),
                'currency_code' => $value->getCurrencyCode(),
            ]
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function productAttributes(): HasOne // Renamed to avoid Eloquent attribute conflict
    {
        return $this->hasOne(ProductAttribute::class);
    }

    public function inventoryMovements(): HasMany
    {
        return $this->hasMany(InventoryMovement::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/placeholder.png'); // Added placeholder
    }

    public function getGalleryImageUrlsAttribute(): array
    {
        $gallery = $this->gallery_images ?? [];
        return array_map(fn($imagePath) => asset('storage/' . $imagePath), $gallery);
    }
}
