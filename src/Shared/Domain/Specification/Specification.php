<?php

declare(strict_types=1);

namespace Ifx\Shared\Domain\Specification;

interface Specification
{
    public function satisfy(): SpecificationResult;
}
