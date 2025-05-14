<?php

declare(strict_types=1);

namespace App\Domain\Payment\DTOs;

use Spatie\LaravelData\Data;

class PaymentIntentResultData extends Data
{
    public function __construct(
        public string $clientSecret,
        public string $paymentIntentId,
        public string $status
    ) {}
}
