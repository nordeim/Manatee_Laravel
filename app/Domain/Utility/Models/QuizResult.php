<?php

declare(strict_types=1);

namespace App\Domain\Utility\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\UserManagement\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuizResult extends Model
{
    use HasFactory;

    const UPDATED_AT = null; // Only created_at in sample

    protected $fillable = [
        'user_id',
        'email',
        'answers',
        'recommendations',
        'created_at',
    ];

    protected $casts = [
        'answers' => 'array',
        'recommendations' => 'array',
        'created_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
