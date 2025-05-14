<?php

declare(strict_types=1);

namespace App\Domain\OrderManagement\Events;

use App\Domain\OrderManagement\Models\Order;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OrderStatusChangedEvent
{
    use Dispatchable, SerializesModels;

    public Order $order;
    public string $oldStatus;
    public array $details; // Optional details about the change

    public function __construct(Order $order, string $oldStatus, array $details = [])
    {
        $this->order = $order;
        $this->oldStatus = $oldStatus;
        $this->details = $details;
    }
}
