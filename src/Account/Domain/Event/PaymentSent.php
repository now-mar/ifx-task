<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\Event;

use Ifx\Account\Domain\Entity\Payment;
use Ifx\Account\Domain\ValueObject\Fee;
use Ifx\Shared\Domain\Event\DomainEvent;
use Ifx\Shared\Domain\ValueObject\AccountId;

final readonly class PaymentSent extends DomainEvent
{
    public function __construct(
        public AccountId $accountId,
        public Payment $payment,
        public Fee $fee,
    ) {
        parent::__construct();
    }
}
