<?php

declare(strict_types=1);

namespace Ifx\Tests\Unit\Account\Domain\Specification;

use Ifx\Account\Domain\Specification\DailySentPaymentsLimitSpecification;
use Ifx\Shared\Domain\Specification\SpecificationResult;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class DailySentPaymentsLimitSpecificationTest extends TestCase
{
    #[DataProvider('data_provider')]
    public function test_spec(int $alreadySentPayments, int $dailyLimits, $specResult)
    {
        $spec = new DailySentPaymentsLimitSpecification($alreadySentPayments, $dailyLimits);
        $this->assertEquals($spec->satisfy(), $specResult);
    }

    public static function data_provider()
    {
        yield 'under daily limit' => [1, 3, SpecificationResult::success()];
        yield 'equal daily limit' => [3, 3, SpecificationResult::error(['Daily sent payments limit exceeded'])];
        yield 'over daily limit' => [4, 3, SpecificationResult::error(['Daily sent payments limit exceeded'])];;
    }
}
