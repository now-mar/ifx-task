<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\Specification;

use Ifx\Shared\Domain\Specification\Specification;
use Ifx\Shared\Domain\Specification\SpecificationResult;

final class DailySentPaymentsLimitSpecification implements Specification
{
    private const MESSAGE = 'Daily sent payments limit exceeded';

    public function __construct(private int $alreadySentPayments, private int $dailyLimits)
    {
    }

    public function satisfy(): SpecificationResult
    {
        return $this->alreadySentPayments < $this->dailyLimits ?
            SpecificationResult::success() :
            SpecificationResult::error([self::MESSAGE]);
    }
}
