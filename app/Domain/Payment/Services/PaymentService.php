<?php

declare(strict_types=1);

namespace App\Domain\Payment\Services;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use App\Domain\Payment\DTOs\PaymentResultData;
use App\Domain\Payment\DTOs\PaymentIntentResultData;

class PaymentService
{
    public function __construct(protected PaymentGateway $gateway)
    {
    }

    public function createPaymentIntent(MonetaryAmount $amount, array $metadata = [], array $customerDetails = []): PaymentIntentResultData
    {
        return $this->gateway->createPaymentIntent($amount, $metadata, $customerDetails);
    }

    public function getPaymentDetails(string $paymentIdentifier): PaymentResultData
    {
        return $this->gateway->getPaymentDetails($paymentIdentifier);
    }

    public function processRefund(string $paymentIdentifier, MonetaryAmount $amount = null, string $reason = null): PaymentResultData
    {
        return $this->gateway->refund($paymentIdentifier, $amount, $reason);
    }

    public function handleWebhook(string $payload, string $signature, string $webhookSecret): object // Type depends on SDK
    {
        // Further processing (dispatching events etc.) would go here based on event type
        return $this->gateway->constructWebhookEvent($payload, $signature, $webhookSecret);
    }
}
