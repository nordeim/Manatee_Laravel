<?php

declare(strict_types=1);

namespace App\Domain\Promotion\Services;

use App\Domain\Promotion\Models\Coupon;
use App\Domain\Shared\ValueObjects\MonetaryAmount;
use App\Domain\UserManagement\Models\User;
use App\Domain\Promotion\Exceptions\InvalidCouponException;
// ... use Order model if checking user uses

class CouponService
{
    public function findAndValidateCoupon(string $code, MonetaryAmount $cartSubtotal, User $user = null): Coupon
    {
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            throw new InvalidCouponException("Coupon '{$code}' not found.");
        }
        if (!$coupon->isValid()) {
            throw new InvalidCouponException("Coupon '{$code}' is no longer valid or has expired.");
        }

        // Check min purchase if applicable
        if ($coupon->min_purchase_minor_amount !== null) {
            if ($coupon->min_purchase_currency_code !== $cartSubtotal->getCurrencyCode()) {
                 throw new InvalidCouponException("Coupon '{$code}' currency mismatch for minimum purchase.");
            }
            if ($cartSubtotal->getMinorAmount() < $coupon->min_purchase_minor_amount) {
                $minPurchase = new MonetaryAmount($coupon->min_purchase_minor_amount, $coupon->min_purchase_currency_code);
                throw new InvalidCouponException("Cart subtotal does not meet minimum purchase of {$minPurchase->format()} for coupon '{$code}'.");
            }
        }

        // Check max uses per user (requires querying user's past orders with this coupon)
        if ($user && $coupon->max_uses_per_user !== null) {
            // $userUses = Order::where('user_id', $user->id)->where('coupon_id', $coupon->id)->count();
            // if ($userUses >= $coupon->max_uses_per_user) {
            //     throw new InvalidCouponException("Coupon '{$code}' usage limit exceeded for this user.");
            // }
        }

        return $coupon;
    }

    public function calculateDiscount(Coupon $coupon, MonetaryAmount $subtotal): MonetaryAmount
    {
        if ($coupon->type === 'percentage') {
            $discountAmount = (int) round(($subtotal->getMinorAmount() * $coupon->value_minor_amount) / 100);
            return new MonetaryAmount($discountAmount, $subtotal->getCurrencyCode());
        } elseif ($coupon->type === 'fixed_amount') {
            if ($coupon->currency_code !== $subtotal->getCurrencyCode()) {
                 throw new InvalidCouponException("Coupon '{$coupon->code}' currency mismatch with cart.");
            }
            // Ensure discount doesn't exceed subtotal
            $discountAmount = min($coupon->value_minor_amount, $subtotal->getMinorAmount());
            return new MonetaryAmount($discountAmount, $subtotal->getCurrencyCode());
        }
        return new MonetaryAmount(0, $subtotal->getCurrencyCode()); // Should not happen
    }

    public function redeemCoupon(Coupon $coupon): void
    {
        $coupon->increment('uses_count');
        // Consider atomicity if high concurrency
    }
}
