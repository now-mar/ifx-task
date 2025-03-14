<?php

declare(strict_types=1);

namespace Ifx\Tests\Unit\Account\Domain\Specification;

use Ifx\Account\Domain\Specification\DailySentPaymentsLimitSpecification;
use Ifx\Account\Domain\Specification\EnoughBalanceSpecification;
use Ifx\Shared\Domain\Specification\SpecificationResult;
use Ifx\Shared\Domain\ValueObject\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class EnoughBalanceSpecificationTest extends TestCase
{
    #[DataProvider('data_provider')]
    public function test_spec($balance, $costs, $specResult)
    {
        $spec = new EnoughBalanceSpecification($balance, ...$costs);
        $this->assertEquals($spec->satisfy(), $specResult);
    }

    public static function data_provider()
    {
        yield 'balance over costs' => [
            Money::PLN(1000),
            [Money::PLN(500), Money::PLN(5)],
            SpecificationResult::success()
        ];
        yield 'balance and costs equal' => [
            Money::PLN(1000),
            [Money::PLN(950), Money::PLN(50)],
            SpecificationResult::success()
        ];
        yield 'costs over balance' => [
            Money::PLN(1000),
            [Money::PLN(950), Money::PLN(51)],
            SpecificationResult::error(['Not enough balance'])
        ];
    }
}
