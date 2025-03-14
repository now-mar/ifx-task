<?php

declare(strict_types=1);

namespace Ifx\Shared\Domain\ValueObject;

class Id
{
    private readonly string $value;

    public function __construct(?string $value = null)
    {
        // comment: here we could also use some lib like symfony/uid or ramsey/uuid to generate (and validate) internal value.
        $this->value = $value ?: uuid();
    }

    public static function fromString(string $value): static
    {
        return new static($value);
    }

    public function equals(mixed $other): bool
    {
        return $other instanceof self && $this->value === $other->value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
