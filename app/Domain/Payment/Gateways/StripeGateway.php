<?php

declare(strict_types=1);

namespace App\Domain\Payment\Gateways;

use App\Domain\Payment\Contracts\PaymentGateway;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use App\Domain\Payment\DTOs\PaymentResultData;
use App\Domain\Payment\DTOs\PaymentIntentResultData;
use Stripe\StripeClient;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;
use Stripe\Webhook;
use Stripe\Event as StripeEvent;

class StripeGateway implements PaymentGateway
{
    protected StripeClient $stripe;

    public function __construct(string $secretKey)
    {
        $this->stripe = new StripeClient($secretKey);
    }

    public function createPaymentIntent(MonetaryAmount $amount, array $metadata = [], array $customerDetails = []): PaymentIntentResultData
    {
        try {
            $params = [
                'amount' => $amount->getMinorAmount(),
                'currency' => strtolower($amount->getCurrencyCode()),
                'automatic_payment_methods' => ['enabled' => true],
                'metadata' => $metadata,
            ];
            // Add customer if details provided
            // if (!empty($customerDetails['email'])) { ... create/retrieve Stripe customer ... }

            $paymentIntent = $this->stripe->paymentIntents->create($params);

            return new PaymentIntentResultData(
                clientSecret: $paymentIntent->client_secret,
                paymentIntentId: $paymentIntent->id,
                status: $paymentIntent->status
            );
        } catch (ApiErrorException $e) {
            // Log error: $e->getMessage()
            throw new \RuntimeException("Stripe PaymentIntent creation failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function getPaymentDetails(string $paymentIntentId): PaymentResultData
    {
        try {
            $paymentIntent = $this->stripe->paymentIntents->retrieve($paymentIntentId);
            return new PaymentResultData(
                id: $paymentIntent->id,
                status: $paymentIntent->status, // e.g., 'succeeded', 'requires_payment_method'
                isSuccess: $paymentIntent->status === PaymentIntent::STATUS_SUCCEEDED,
                amountMinor: $paymentIntent->amount,
                currency: strtoupper($paymentIntent->currency),
                gatewayResponse: $paymentIntent->toArray()
            );
        } catch (ApiErrorException $e) {
            throw new \RuntimeException("Failed to retrieve Stripe PaymentIntent: " . $e->getMessage(), 0, $e);
        }
    }


    public function refund(string $paymentIntentId, MonetaryAmount $amount = null, string $reason = null): PaymentResultData
    {
        try {
            $params = ['payment_intent' => $paymentIntentId];
            if ($amount) {
                $params['amount'] = $amount->getMinorAmount();
            }
            if ($reason) {
                $params['reason'] = $reason;
            }
            $refund = $this->stripe->refunds->create($params);
            return new PaymentResultData(
                id: $refund->id, // Refund ID
                status: $refund->status,
                isSuccess: $refund->status === 'succeeded',
                amountMinor: $refund->amount,
                currency: strtoupper($refund->currency),
                gatewayResponse: $refund->toArray(),
                originalPaymentId: $paymentIntentId
            );
        } catch (ApiErrorException $e) {
             throw new \RuntimeException("Stripe refund failed: " . $e->getMessage(), 0, $e);
        }
    }

    public function constructWebhookEvent(string $payload, string $signature, string $webhookSecret): StripeEvent
    {
        return Webhook::constructEvent($payload, $signature, $webhookSecret);
    }
}
