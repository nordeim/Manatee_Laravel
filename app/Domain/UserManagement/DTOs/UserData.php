<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UserData extends Data
{
    public function __construct(
        public string $name,
        public string $email,
        public string|Optional $password, // Optional for updates
        public string|Optional $role,
        public string|Optional $status,
        public bool|Optional $newsletter_subscribed,
        // Add address fields if creating user with address directly, or handle addresses separately
        // public string|Optional $address_line1,
        // ...
    ) {}

    public static function rules(): array
    {
        // Basic rules, more specific rules in FormRequests
        return [
            'name' => ['required', 'string', 'max:191'],
            'email' => ['required', 'email', 'max:191'],
            // Password rules would differ for creation vs update
        ];
    }
}
