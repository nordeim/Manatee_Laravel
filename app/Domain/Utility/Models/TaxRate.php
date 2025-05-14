<?php

declare(strict_types=1);

namespace App\Domain\Utility\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\UserManagement\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TaxRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'country_code',
        'state_code',
        'postal_code_pattern',
        'city',
        'rate_percentage',
        'is_compound',
        'priority',
        'is_active',
        'start_date',
        'end_date',
        'created_by_user_id',
    ];

    protected $casts = [
        'rate_percentage' => 'decimal:4',
        'is_compound'     => 'boolean',
        'is_active'       => 'boolean',
        'start_date'      => 'date',
        'end_date'        => 'date',
        'priority'        => 'integer',
    ];

    public function createdByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_user_id');
    }

    public function history(): HasMany
    {
        return $this->hasMany(TaxRateHistory::class);
    }
}
