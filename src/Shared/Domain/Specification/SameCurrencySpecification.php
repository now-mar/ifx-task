<?php

declare(strict_types=1);

namespace Ifx\Shared\Domain\Specification;

use Ifx\Shared\Domain\ValueObject\Currency;

final class SameCurrencySpecification implements Specification
{
    private const MESSAGE = 'Currency mismatch';

    public function __construct(private Currency $firstCurrency, private Currency $secondCurrency)
    {

    }

    public function satisfy(): SpecificationResult
    {
        return $this->firstCurrency->equals($this->secondCurrency) ?
            SpecificationResult::success() :
            SpecificationResult::error([self::MESSAGE]);
    }
}
