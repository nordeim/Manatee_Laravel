<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use Stringable;

readonly class FullName implements Stringable
{
    public function __construct(
        public string $firstName,
        public ?string $middleName = null,
        public string $lastName
    ) {
    }

    public function getFullName(): string
    {
        return trim($this->firstName . ($this->middleName ? ' ' . $this->middleName : '') . ' ' . $this->lastName);
    }

    public function __toString(): string
    {
        return $this->getFullName();
    }
}
