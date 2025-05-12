<?php

namespace App\Casts;

use App\Domain\Shared\ValueObjects\MonetaryAmount;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class MonetaryAmountCast implements CastsAttributes
{
    protected string $currencyColumnOrCode;

    /**
     * Create a new cast instance.
     *
     * @param string $currencyColumnOrCode The name of the column holding the currency code, or the currency code itself.
     */
    public function __construct(string $currencyColumnOrCode = 'currency_code')
    {
        $this->currencyColumnOrCode = $currencyColumnOrCode;
    }

    public function get(Model $model, string $key, mixed $value, array $attributes): ?MonetaryAmount
    {
        if ($value === null) {
            return null;
        }

        $currencyCode = $this->resolveCurrencyCode($model, $attributes);

        return new MonetaryAmount((int) $value, $currencyCode);
    }

    public function set(Model $model, string $key, mixed $value, array $attributes): array
    {
        if ($value === null) {
            return [$key => null];
        }

        if (!$value instanceof MonetaryAmount) {
            throw new InvalidArgumentException('The given value is not a MonetaryAmount instance.');
        }

        $dataToSet = [$key => $value->getMinorAmount()];

        // If currencyColumnOrCode refers to a column name, set it.
        // Otherwise, we assume it was a fixed currency code passed to the constructor,
        // and the model should have a separate currency_code column managed elsewhere or fixed.
        if ($this->isColumnReference($model)) {
             $dataToSet[$this->currencyColumnOrCode] = $value->getCurrencyCode();
        } elseif (strtoupper($this->currencyColumnOrCode) !== $value->getCurrencyCode()) {
            // If a fixed currency was given in cast and it doesn't match, it's an issue.
            // However, usually the model has a dedicated currency_code column.
            // This cast is simpler if we assume currency_code is another column.
            // For a single fixed currency, e.g. MonetaryAmountCast::class.':USD'
            // we'd rely on the constructor argument being the code.
        }


        return $dataToSet;
    }

    protected function resolveCurrencyCode(Model $model, array $attributes): string
    {
        // Check if currencyColumnOrCode is an actual column name in attributes or model
        if (array_key_exists($this->currencyColumnOrCode, $attributes)) {
            return $attributes[$this->currencyColumnOrCode];
        }
        if (isset($model->{$this->currencyColumnOrCode})) {
            return $model->{$this->currencyColumnOrCode};
        }
        // Assume it's a currency code string if not a column
        return $this->currencyColumnOrCode;
    }

    protected function isColumnReference(Model $model): bool
    {
        // A simple check: if it's not a 3-letter uppercase string, assume it's a column name.
        // A more robust check might involve checking $model->getFillable() or schema.
        return !(strlen($this->currencyColumnOrCode) === 3 && strtoupper($this->currencyColumnOrCode) === $this->currencyColumnOrCode);
    }
}
