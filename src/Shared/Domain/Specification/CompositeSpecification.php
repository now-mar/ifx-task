<?php

declare(strict_types=1);

namespace Ifx\Shared\Domain\Specification;

final class CompositeSpecification implements Specification
{
    private array $specification;

    public function __construct(Specification ...$specification)
    {
        $this->specification = $specification;
    }

    public function satisfy(): SpecificationResult
    {
        foreach ($this->specification as $specification) {
            $result = $specification->satisfy();
            if (!$result->satisfied) {
                return $result;
            }
        }

        return SpecificationResult::success();
    }
}
