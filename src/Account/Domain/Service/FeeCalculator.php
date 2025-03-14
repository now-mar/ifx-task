<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\Service;

use Ifx\Account\Domain\Entity\Payment;
use Ifx\Account\Domain\ValueObject\Fee;
use Ifx\Shared\Domain\ValueObject\Money;

interface FeeCalculator
{
    public function calculate(Payment $payment): Fee;
}
