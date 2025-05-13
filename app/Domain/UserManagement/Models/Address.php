<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domain\UserManagement\Models\User;

class Address extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'company',
        'address_line1',
        'address_line2',
        'city',
        'state',
        'postal_code',
        'country_code',
        'phone',
        'type',
        'is_default_shipping',
        'is_default_billing'
    ];

    protected $casts = [
        'is_default_shipping' => 'boolean',
        'is_default_billing' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFormattedLinesAttribute(): array
    {
        $lines = [];
        if ($this->first_name || $this->last_name) {
            $lines[] = trim($this->first_name . ' ' . $this->last_name);
        }
        if ($this->company) {
            $lines[] = $this->company;
        }
        $lines[] = $this->address_line1;
        if ($this->address_line2) {
            $lines[] = $this->address_line2;
        }
        $lines[] = trim("{$this->city}, {$this->state} {$this->postal_code}");
        $lines[] = $this->country_code; // Consider converting to full country name using a helper
        return array_filter($lines);
    }

    public function getFormattedHtmlAttribute(): string
    {
        return implode('<br>', $this->getFormattedLinesAttribute());
    }

    public function __toString(): string
    {
        return implode("\n", $this->getFormattedLinesAttribute());
    }
}
