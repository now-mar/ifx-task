<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\Entity;

use DateTimeImmutable;
use Ifx\Account\Domain\ValueObject\PaymentId;
use Ifx\Shared\Domain\ValueObject\Money;

final class Payment
{
    public readonly PaymentId $id;

    public function __construct(
        public Money $value,
    ) {
        if ($this->value->value <= 0) {
            throw new \DomainException('Payment value must be greater than 0');
        }

        $this->id = new PaymentId();
    }
}
