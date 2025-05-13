<?php

declare(strict_types=1);

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use Stringable;

readonly class EmailAddress implements Stringable
{
    public string $value;
    public string $localPart;
    public string $domainPart;

    public function __construct(string $email)
    {
        $email = trim(strtolower($email));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException("Invalid email address format: {$email}");
        }
        $this->value = $email;
        [$this->localPart, $this->domainPart] = explode('@', $this->value, 2);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getLocalPart(): string
    {
        return $this->localPart;
    }

    public function getDomainPart(): string
    {
        return $this->domainPart;
    }

    public function isEqualTo(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
