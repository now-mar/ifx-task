<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\ValueObject;

use Ifx\Shared\Domain\ValueObject\Money;

final readonly class Fee
{
    public function __construct(
        public Money $value,
        public FeeType $feeType,
    ) {
        if ($this->value->value <= 0) {
            throw new \DomainException('Fee value must be greater than 0');
        }
    }
}
