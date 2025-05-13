<?php

declare(strict_types=1);

namespace App\Domain\UserManagement\Services;

use App\Domain\UserManagement\Models\User;
use App\Domain\UserManagement\DTOs\UserData;
use Illuminate\Support\Facades\Hash;

class UserProfileService
{
    public function updateUser(User $user, UserData $userData): User
    {
        $updateData = [
            'name' => $userData->name,
            'email' => $userData->email,
        ];

        if (!($userData->password instanceof \Spatie\LaravelData\Optional) && $userData->password) {
            $updateData['password'] = Hash::make($userData->password);
        }
        if (!($userData->newsletter_subscribed instanceof \Spatie\LaravelData\Optional)) {
            $updateData['newsletter_subscribed'] = $userData->newsletter_subscribed;
        }
        // Add other updatable fields based on UserData

        $user->update($updateData);
        return $user->refresh();
    }

    public function createUser(UserData $userData): User
    {
        return User::create([
            'name' => $userData->name,
            'email' => $userData->email,
            'password' => Hash::make($userData->password), // Assuming password is required for creation
            'role' => $userData->role instanceof \Spatie\LaravelData\Optional ? 'user' : $userData->role,
            'status' => $userData->status instanceof \Spatie\LaravelData\Optional ? 'active' : $userData->status,
            'newsletter_subscribed' => $userData->newsletter_subscribed instanceof \Spatie\LaravelData\Optional ? false : $userData->newsletter_subscribed,
        ]);
    }

    // Methods for managing addresses can go here or in a dedicated AddressService
}
