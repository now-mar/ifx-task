<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\Specification;

use Ifx\Shared\Domain\Specification\Specification;
use Ifx\Shared\Domain\Specification\SpecificationResult;
use Ifx\Shared\Domain\ValueObject\Money;

final class EnoughBalanceSpecification implements Specification
{
    private const MESSAGE = 'Not enough balance';

    private Money $currentBalance;
    private array $paymentCosts;

    public function __construct(Money $currentBalance, Money ...$paymentCosts)
    {
        $this->currentBalance = $currentBalance;
        $this->paymentCosts = $paymentCosts;
    }

    public function satisfy(): SpecificationResult
    {
        $paymentCostsSum = new Money(0, $this->currentBalance->currency);
        foreach ($this->paymentCosts as $paymentCost) {
            $paymentCostsSum = $paymentCostsSum->add($paymentCost);
        }

        return $this->currentBalance->gte($paymentCostsSum) ?
            SpecificationResult::success() :
            SpecificationResult::error([self::MESSAGE]);
    }
}
