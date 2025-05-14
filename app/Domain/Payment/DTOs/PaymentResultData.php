<?php

declare(strict_types=1);

namespace App\Domain\Payment\DTOs;

use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class PaymentResultData extends Data
{
    public function __construct(
        public string $id, // Gateway's transaction/refund ID
        public string $status, // Gateway's status
        public bool $isSuccess,
        public int|Optional $amountMinor,
        public string|Optional $currency,
        public array|Optional $gatewayResponse, // Raw response from gateway
        public string|Optional $originalPaymentId, // For refunds
        public string|Optional $errorMessage,
    ) {}
}
