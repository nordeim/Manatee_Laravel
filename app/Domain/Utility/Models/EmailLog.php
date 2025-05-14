<?php

declare(strict_types=1);

namespace App\Domain\Utility\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domain\UserManagement\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailLog extends Model
{
    use HasFactory;

    // Sample schema does not have updated_at for email_log
    const UPDATED_AT = null;

    protected $fillable = [
        'user_id',
        'to_email',
        'subject',
        'email_type',
        'status',
        'mailer_error',
        'sent_at',
        'created_at',
    ];

    protected $casts = [
        'sent_at'   => 'datetime',
        'created_at'=> 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
