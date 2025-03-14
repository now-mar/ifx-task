<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\Service;

use Ifx\Account\Domain\Entity\Payment;
use Ifx\Account\Domain\ValueObject\Fee;
use Ifx\Account\Domain\ValueObject\FeeType;
use Ifx\Shared\Domain\ValueObject\Money;

final class TransactionCostsFeeCalculator implements FeeCalculator
{
    public function __construct(private string $transactionCostsFee)
    {
    }

    public function calculate(Payment $payment): Fee
    {
        $amount = new Money((int) round($payment->value->value * $this->transactionCostsFee), $payment->value->currency);

        return new Fee($amount, FeeType::TransactionCosts);
    }
}
