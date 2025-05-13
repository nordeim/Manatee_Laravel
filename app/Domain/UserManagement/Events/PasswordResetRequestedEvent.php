<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\Events;

use App\Domain\UserManagement\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PasswordResetRequestedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $token;

    public function __construct(User $user, string $token)
    {
        $this->user = $user;
        $this->token = $token;
    }
}
