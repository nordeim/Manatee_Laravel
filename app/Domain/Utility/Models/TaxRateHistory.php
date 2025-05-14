<?php

declare(strict_types=1);

namespace App\Domain\Utility\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\UserManagement\Models\User;
use App\Domain\Utility\Models\TaxRate;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TaxRateHistory extends Model
{
    use HasFactory;

    const UPDATED_AT = null;

    protected $fillable = [
        'tax_rate_id',
        'old_rate_percentage',
        'new_rate_percentage',
        'changed_by_user_id',
        'created_at',
    ];

    protected $casts = [
        'old_rate_percentage' => 'decimal:4',
        'new_rate_percentage' => 'decimal:4',
        'created_at'          => 'datetime',
    ];

    public function taxRate(): BelongsTo
    {
        return $this->belongsTo(TaxRate::class);
    }

    public function changedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by_user_id');
    }
}
