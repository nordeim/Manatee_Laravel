<?php

declare(strict_types=1);

namespace App\Domain\Payment\Events;

use App\Domain\Payment\DTOs\PaymentResultData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets; // Optional: if you plan to broadcast this event

class PaymentSuccessfulEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public PaymentResultData $paymentResult,
        public ?int $orderId = null // Or reference to Order model if preferred and always available
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    // public function broadcastOn(): array
    // {
    //     // Example: return [new \Illuminate\Broadcasting\PrivateChannel('channel-name')];
    //     return [];
    // }
}
