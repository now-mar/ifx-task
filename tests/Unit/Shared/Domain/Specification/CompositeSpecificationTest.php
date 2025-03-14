<?php

declare(strict_types=1);

use Ifx\Shared\Domain\Specification\CompositeSpecification;
use Ifx\Shared\Domain\Specification\Specification;
use Ifx\Shared\Domain\Specification\SpecificationResult;
use PHPUnit\Framework\TestCase;

final class CompositeSpecificationTest extends TestCase
{
    public function test_spec_returns_first_failed_result()
    {
        $spec1 = new class() implements Specification {
            public function satisfy(): SpecificationResult
            {
                return SpecificationResult::error(['error 1']);
            }
        };

        $spec2 = new class() implements Specification {
            public function satisfy(): SpecificationResult
            {
                return SpecificationResult::error(['error 2']);
            }
        };

        $spec = new CompositeSpecification($spec1, $spec2);
        $specResult = $spec->satisfy();
        $this->assertEquals(SpecificationResult::error(['error 1']), $specResult);
    }

    public function test_spec_returns_succes_when_no_error()
    {
        $spec1 = new class() implements Specification {
            public function satisfy(): SpecificationResult
            {
                return SpecificationResult::success();
            }
        };

        $spec2 = new class() implements Specification {
            public function satisfy(): SpecificationResult
            {
                return SpecificationResult::success();
            }
        };

        $spec = new CompositeSpecification($spec1, $spec2);
        $specResult = $spec->satisfy();
        $this->assertEquals(SpecificationResult::success(), $specResult);
    }
}

