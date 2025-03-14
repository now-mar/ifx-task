<?php

declare(strict_types=1);

namespace Ifx\Tests\Unit\Account\Domain\Service;

use Ifx\Account\Domain\Entity\Payment;
use Ifx\Account\Domain\Service\TransactionCostsFeeCalculator;
use Ifx\Account\Domain\ValueObject\Fee;
use Ifx\Account\Domain\ValueObject\FeeType;
use Ifx\Shared\Domain\ValueObject\Currency;
use Ifx\Shared\Domain\ValueObject\Money;
use PHPUnit\Framework\TestCase;

final class TransactionCostsFeeCalculatorTest extends TestCase
{
    public function test_calculate()
    {
        $pln = new Currency('PLN');
        $payment = new Payment(new Money(1000, $pln));
        $fee = new TransactionCostsFeeCalculator('0.005')->calculate($payment);

        $feeValue = (int) round(1000 * 0.005);
        $this->assertEquals(new Fee(new Money($feeValue, $pln), FeeType::TransactionCosts), $fee);
    }
}
