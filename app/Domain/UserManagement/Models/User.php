<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Domain\OrderManagement\Models\Order;
use App\Domain\UserManagement\Models\Address;
use App\Domain\Utility\Models\QuizResult;
use App\Domain\Utility\Models\AuditLog;
use App\Domain\Utility\Models\EmailLog;
use App\Domain\Utility\Models\TaxRate; // For created_by_user_id
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable implements MustVerifyEmail // Add MustVerifyEmail if you require it
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'newsletter_subscribed',
        'default_shipping_address_id', // Added
        'default_billing_address_id',  // Added
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'reset_token', // From sample schema
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'newsletter_subscribed' => 'boolean',
        'reset_token_expires_at' => 'datetime', // From sample schema
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function defaultShippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'default_shipping_address_id');
    }

    public function defaultBillingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'default_billing_address_id');
    }

    public function quizResults(): HasMany
    {
        return $this->hasMany(QuizResult::class);
    }

    public function auditLogs(): HasMany
    {
        // Assuming audit_log user_id can be null if action is system generated
        return $this->hasMany(AuditLog::class, 'user_id');
    }

    public function emailLogs(): HasMany
    {
        return $this->hasMany(EmailLog::class, 'user_id');
    }

    public function taxRatesCreated(): HasMany
    {
        return $this->hasMany(TaxRate::class, 'created_by_user_id');
    }

    // Add other relationships as needed from the schema, e.g., for cart if persistent
}
