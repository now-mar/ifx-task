<?php

declare(strict_types=1);

use Ifx\Account\Domain\Specification\DailySentPaymentsLimitSpecification;
use Ifx\Shared\Domain\Specification\SameCurrencySpecification;
use Ifx\Shared\Domain\Specification\SpecificationResult;
use Ifx\Shared\Domain\ValueObject\Currency;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class SameCurrencySpecificationTest extends TestCase
{
    #[DataProvider('data_provider')]
    public function test_spec(Currency $curr1, Currency $curr2, $specResult)
    {
        $spec = new SameCurrencySpecification($curr1, $curr2);
        $this->assertEquals($spec->satisfy(), $specResult);
    }

    public static function data_provider()
    {
        yield 'same currency' => [new Currency('PLN'), new Currency('PLN'), SpecificationResult::success()];
        yield 'different currency' => [new Currency('PLN'), new Currency('EUR'), SpecificationResult::error(['Currency mismatch'])];
    }
}
