<?php

declare(strict_types=1);

namespace App\Domain\Payment\Events;

use App\Domain\Payment\DTOs\PaymentResultData;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\InteractsWithSockets; // Optional

class PaymentFailedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public PaymentResultData $paymentResult,
        public ?int $orderId = null, // Or Order model
        public ?string $reason = null
    ) {}

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    // public function broadcastOn(): array
    // {
    //     return [];
    // }
}
