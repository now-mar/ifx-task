<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\ValueObject;

final readonly class AccountRules
{
    public function __construct(
        public int $dailySentPaymentsLimit,
        public string $sendPaymentFee
    ) {
        if ($this->dailySentPaymentsLimit < 0) {
            throw new \InvalidArgumentException('Daily sent payments limit must be greater than 0');
        }

        if ($this->sendPaymentFee < 0) {
            throw new \InvalidArgumentException('Send payment fee must be greater than 0');
        }
    }
}
