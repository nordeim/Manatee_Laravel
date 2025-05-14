<?php

declare(strict_types=1);

namespace App\Domain\Payment\Contracts;

use App\Domain\Shared\ValueObjects\MonetaryAmount;
use App\Domain\Payment\DTOs\PaymentResultData;
use App\Domain\Payment\DTOs\PaymentIntentResultData;


interface PaymentGateway
{
    /**
     * Create a payment intent (or equivalent) for the given amount.
     */
    public function createPaymentIntent(MonetaryAmount $amount, array $metadata = [], array $customerDetails = []): PaymentIntentResultData;

    /**
     * Process a payment (e.g., capture a payment intent, charge a card directly).
     * This might not be used if using client-side confirmation with PaymentIntents.
     */
    // public function charge(MonetaryAmount $amount, string $paymentMethodId, array $customerDetails = []): PaymentResultData;

    /**
     * Retrieve payment details for a given payment identifier.
     */
    public function getPaymentDetails(string $paymentIdentifier): PaymentResultData;


    /**
     * Process a refund.
     */
    public function refund(string $paymentIdentifier, MonetaryAmount $amount = null, string $reason = null): PaymentResultData;

    /**
     * Construct and verify a webhook event.
     */
    public function constructWebhookEvent(string $payload, string $signature, string $webhookSecret): object; // Type depends on gateway SDK
}
