<?php

declare(strict_types=1);

namespace App\Domain\Utility\Services;

use App\Domain\Shared\ValueObjects\MonetaryAmount;
use App\Domain\Shared\ValueObjects\AddressDetail; // Or use the Address Model
use App\Domain\Utility\Models\TaxRate;
use App\Domain\Catalog\Models\Product; // Optional, if taxes are product-specific
use Illuminate\Support\Collection;

class TaxService
{
    public function calculateTax(MonetaryAmount $subtotalAmount, AddressDetail $shippingAddress, Collection $cartItems = null): MonetaryAmount
    {
        // 1. Determine applicable tax rates based on shippingAddress
        //    (country_code, state_code, postal_code_pattern, city)
        //    This could involve complex lookups against the tax_rates table.
        // 2. Consider if tax is on shipping, product-specific tax categories.
        // 3. Handle compound taxes if `is_compound` is true.

        // Simplified example: Find first matching active tax rate
        $applicableRate = TaxRate::where('is_active', true)
            ->where('country_code', $shippingAddress->countryCode)
            // Add more precise matching for state, postal code, city if data available
            // This needs to be more robust for real-world tax calculation.
            ->when($shippingAddress->state, function ($query, $state) {
                return $query->where(function ($q) use ($state) {
                    $q->where('state_code', $state)->orWhereNull('state_code');
                });
            })
            ->orderBy('priority') // Apply higher priority taxes first
            ->first();

        if ($applicableRate) {
            $taxMinorAmount = (int) round(
                ($subtotalAmount->getMinorAmount() * $applicableRate->rate_percentage) / 100
            );
            return new MonetaryAmount($taxMinorAmount, $subtotalAmount->getCurrencyCode());
        }

        // Default to zero tax if no rate found or for more complex scenarios
        return new MonetaryAmount(0, $subtotalAmount->getCurrencyCode());
    }
}
