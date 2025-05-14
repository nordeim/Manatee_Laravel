<?php

declare(strict_types=1);

namespace App\Domain\Utility\Services;

use App\Domain\Utility\Models\NewsletterSubscriber;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
// use App\Events\NewsletterSubscribed; // Example event

class NewsletterService
{
    public function subscribe(string $email): NewsletterSubscriber
    {
        $normalizedEmail = strtolower(trim($email));

        Log::info("Attempting to subscribe email: {$normalizedEmail}");

        $subscriber = NewsletterSubscriber::firstOrCreate(
            ['email' => $normalizedEmail],
            [
                'subscribed_at' => now(), // Eloquent will manage created_at/updated_at
                'token' => Str::random(60)
            ]
        );

        if (!$subscriber->wasRecentlyCreated && $subscriber->unsubscribed_at) {
            // Re-subscribing an unsubscribed email
            $subscriber->unsubscribed_at = null;
            $subscriber->subscribed_at = now(); // Reset subscribed_at
            $subscriber->token = Str::random(60); // Optionally generate a new token
            $subscriber->save();
            Log::info("Email re-subscribed: {$normalizedEmail}");
        } elseif ($subscriber->wasRecentlyCreated) {
             Log::info("New email subscribed: {$normalizedEmail}");
            // event(new NewsletterSubscribed($subscriber)); // Dispatch an event
        } else {
             Log::info("Email already subscribed: {$normalizedEmail}");
        }

        return $subscriber;
    }

    public function unsubscribe(string $token): bool
    {
        $subscriber = NewsletterSubscriber::where('token', $token)
                                          ->whereNull('unsubscribed_at')
                                          ->first();
        if ($subscriber) {
            $subscriber->unsubscribed_at = now();
            $isSaved = $subscriber->save();
            if ($isSaved) {
                Log::info("Email unsubscribed using token: {$subscriber->email}");
            }
            return $isSaved;
        }
        Log::warning("Unsubscribe attempt with invalid or already used token: {$token}");
        return false;
    }
}
