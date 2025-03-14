<?php

declare(strict_types=1);

namespace Ifx\Shared\Domain\Exception;

final class SpecificationException extends \DomainException
{
    public function __construct(
        /** @var string[] */
        public readonly array $errors
    ) {
        parent::__construct('Specification failed.');
    }
}
