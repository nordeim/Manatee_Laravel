<?php

declare(strict_types=1);

namespace App\Domain\Promotion\Exceptions;

use RuntimeException;
use Throwable;

class InvalidCouponException extends RuntimeException
{
    /**
     * Create a new exception instance.
     *
     * @param string $message
     * @param int $code
     * @param \Throwable|null $previous
     * @return void
     */
    public function __construct(string $message = "The provided coupon is invalid or cannot be applied.", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    // You could add static factory methods for common invalid coupon reasons:
    // public static function notFound(string $code): self
    // {
    //     return new self("Coupon '{$code}' not found.");
    // }
    //
    // public static function expired(string $code): self
    // {
    //     return new self("Coupon '{$code}' has expired.");
    // }
    //
    // public static function usageLimitReached(string $code): self
    // {
    //     return new self("Coupon '{$code}' has reached its usage limit.");
    // }
    //
    // public static function minimumSpendNotMet(string $code, string $minimumSpendFormatted): self
    // {
    //     return new self("A minimum spend of {$minimumSpendFormatted} is required to use coupon '{$code}'.");
    // }
}
