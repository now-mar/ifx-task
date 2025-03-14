<?php

declare(strict_types=1);

namespace Ifx\Shared\Domain\ValueObject;

final readonly class Money
{
    public function __construct(
        public int $value,
        public Currency $currency
    ) {
        if ($this->value < 0) {
            throw new \InvalidArgumentException('Value cannot be negative');
        }
    }

    public function add(self $other)
    {
        $this->validateCurrency($other);

        return new self($this->value + $other->value, $this->currency);
    }

    public function sub(self $other)
    {
        $this->validateCurrency($other);

        return new self($this->value + $other->value, $this->currency);
    }

    public function gte(Money $other): bool
    {
        $this->validateCurrency($other);

        return $this->value >= $other->value;
    }

    private function validateCurrency(self $other): void
    {
        if (!$this->currency->equals($other->currency)) {
            throw new \InvalidArgumentException('Currency mismatch');
        }
    }

    public static function PLN(int $value): self
    {
        return new self($value, new Currency('PLN'));
    }
}
