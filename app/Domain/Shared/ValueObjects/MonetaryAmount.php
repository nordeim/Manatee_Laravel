<?php

namespace App\Domain\Shared\ValueObjects;

use Brick\Money\Money as BrickMoney;
use Brick\Money\Context\DefaultContext;
use Brick\Money\Exception\UnknownCurrencyException;
use InvalidArgumentException;

readonly class MonetaryAmount
{
    private BrickMoney $money;

    /**
     * @throws UnknownCurrencyException
     */
    public function __construct(int $minorAmount, string $currencyCode)
    {
        if (empty(trim($currencyCode))) {
            throw new InvalidArgumentException('Currency code cannot be empty.');
        }
        // BrickMoney will validate the currency code.
        $this->money = BrickMoney::ofMinor($minorAmount, strtoupper($currencyCode));
    }

    /**
     * @throws UnknownCurrencyException
     */
    public static function fromDecimal(string|float|int $amount, string $currencyCode): self
    {
        if (empty(trim($currencyCode))) {
            throw new InvalidArgumentException('Currency code cannot be empty.');
        }
        return new self(
            BrickMoney::of($amount, strtoupper($currencyCode), new DefaultContext())->getMinorAmount()->toInt(),
            strtoupper($currencyCode)
        );
    }

    public function getMinorAmount(): int
    {
        return $this->money->getMinorAmount()->toInt();
    }

    public function getCurrencyCode(): string
    {
        return $this->money->getCurrency()->getCurrencyCode();
    }

    public function getDecimalAmount(): string
    {
        return (string) $this->money->getAmount();
    }

    public function add(MonetaryAmount $other): self
    {
        if ($this->getCurrencyCode() !== $other->getCurrencyCode()) {
            throw new InvalidArgumentException('Cannot add amounts with different currencies: ' . $this->getCurrencyCode() . ' and ' . $other->getCurrencyCode());
        }
        return new self($this->money->plus($other->money)->getMinorAmount()->toInt(), $this->getCurrencyCode());
    }

    public function subtract(MonetaryAmount $other): self
    {
        if ($this->getCurrencyCode() !== $other->getCurrencyCode()) {
            throw new InvalidArgumentException('Cannot subtract amounts with different currencies.');
        }
        return new self($this->money->minus($other->money)->getMinorAmount()->toInt(), $this->getCurrencyCode());
    }

    public function multiplyBy(string|int|float $multiplier): self
    {
        return new self($this->money->multipliedBy($multiplier)->getMinorAmount()->toInt(), $this->getCurrencyCode());
    }

    public function isEqualTo(MonetaryAmount $other): bool
    {
        return $this->money->isEqualTo($other->money);
    }

    public function isGreaterThan(MonetaryAmount $other): bool
    {
        return $this->money->isGreaterThan($other->money);
    }

    public function isLessThan(MonetaryAmount $other): bool
    {
        return $this->money->isLessThan($other->money);
    }

    public function format(string $locale = 'en_US'): string
    {
        // Consider using a more robust localization solution for formatting in a real app
        // For now, relying on Brick\Money's default formatting which might not be fully locale-aware for all symbols/patterns.
        // NumberFormatter from PHP's intl extension is generally better for this.
        return $this->money->formatTo($locale); // Default Brick/Money formatting
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
