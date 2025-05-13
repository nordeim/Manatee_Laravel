<?php

declare(strict_types=1);

namespace App\Domain\Catalog\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductAttribute extends Model
{
    use HasFactory;

    protected $table = 'product_attributes'; // Explicitly define table name

    protected $fillable = [
        'product_id',
        'scent_type',
        'mood_effect',
        'intensity_level',
    ];

    // No casts needed for enums if they are stored as strings and handled by DB

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
