<?php

declare(strict_types=1);

namespace Ifx\Shared\Domain\ValueObject;

final class Currency
{
    private static array $currencies = [
        'PLN',
        'EUR',
        'USD',
        'CHF',
    ];

    public readonly string $code;

    public function __construct(string $code)
    {
        if (!in_array($code, self::$currencies)) {
            throw new \InvalidArgumentException('Unknown currency code: ' . $code);
        }

        $this->code = strtoupper($code);
    }

    public function equals(mixed $other): bool
    {
        return $other instanceof self && $this->code === $other->code;
    }
}
