<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\UserManagement\Models\User;
// use App\Domain\UserManagement\Models\Address; // If using Address model for orders
use App\Domain\Promotion\Models\Coupon;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute as EloquentAttribute;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number', 'user_id', 'guest_email',
        // Direct address fields from sample schema
        'shipping_name', 'shipping_email', 'shipping_address', 'shipping_address_line2',
        'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country',
        // 'shipping_address_id', 'billing_address_id', // If using Address model FKs
        'status', 'payment_status', 'payment_intent_id', 'payment_gateway',
        'subtotal_minor_amount', 'discount_minor_amount', 'coupon_id', 'coupon_code_applied',
        'shipping_cost_minor_amount', 'tax_minor_amount', 'total_minor_amount', 'currency_code',
        'order_notes', 'paid_at', 'dispute_id', 'disputed_at', 'refund_id', 'refunded_at',
        'tracking_number', 'carrier',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'disputed_at' => 'datetime',
        'refunded_at' => 'datetime',
        'subtotal_minor_amount' => 'integer',
        'discount_minor_amount' => 'integer',
        'shipping_cost_minor_amount' => 'integer',
        'tax_minor_amount' => 'integer',
        'total_minor_amount' => 'integer',
    ];

    // Virtual attributes for MonetaryAmount objects
    protected function subtotal(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['subtotal_minor_amount'], $attributes['currency_code'])
        );
    }

    protected function discountAmount(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['discount_minor_amount'], $attributes['currency_code'])
        );
    }

    protected function shippingCost(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['shipping_cost_minor_amount'], $attributes['currency_code'])
        );
    }

    protected function taxAmount(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['tax_minor_amount'], $attributes['currency_code'])
        );
    }

    protected function totalAmount(): EloquentAttribute
    {
        return EloquentAttribute::make(
            get: fn ($value, $attributes) => new MonetaryAmount((int) $attributes['total_minor_amount'], $attributes['currency_code'])
        );
    }


    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function coupon(): BelongsTo
    {
        return $this->belongsTo(Coupon::class);
    }

    // If using Address model for shipping/billing on orders:
    // public function shippingAddressResolved(): BelongsTo
    // {
    //     return $this->belongsTo(Address::class, 'shipping_address_id');
    // }
    // public function billingAddressResolved(): BelongsTo
    // {
    //     return $this->belongsTo(Address::class, 'billing_address_id');
    // }
}
