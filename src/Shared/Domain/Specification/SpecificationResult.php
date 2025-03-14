<?php

declare(strict_types=1);

namespace Ifx\Shared\Domain\Specification;

final readonly class SpecificationResult
{
    private function __construct(
        public bool $satisfied,
        /** @var string[] */
        public array $errors = []
    ) {
    }

    public static function success(): self
    {
        return new self(true);
    }

    /**
     * @param string[] $errors
     */
    public static function error(array $errors): self
    {
        return new self(false, $errors);
    }
}
