<?php

declare(strict_types=1);

namespace App\Domain\Promotion\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\Shared\ValueObjects\MonetaryAmount; // If needed for value or min_purchase
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    protected $fillable = [
        'code', 'description', 'type', 'value_minor_amount', 'currency_code',
        'max_uses', 'uses_count', 'max_uses_per_user',
        'min_purchase_minor_amount', 'min_purchase_currency_code',
        'valid_from', 'valid_to', 'is_active',
    ];

    protected $casts = [
        'value_minor_amount' => 'integer',
        'max_uses' => 'integer',
        'uses_count' => 'integer',
        'max_uses_per_user' => 'integer',
        'min_purchase_minor_amount' => 'integer',
        'valid_from' => 'datetime',
        'valid_to' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function isValid(): bool
    {
        if (!$this->is_active) {
            return false;
        }
        if ($this->valid_from && Carbon::now()->lt($this->valid_from)) {
            return false;
        }
        if ($this->valid_to && Carbon::now()->gt($this->valid_to)) {
            return false;
        }
        if ($this->max_uses !== null && $this->uses_count >= $this->max_uses) {
            return false;
        }
        return true;
    }

    // Add methods for checking user specific limits, min purchase etc.
}
