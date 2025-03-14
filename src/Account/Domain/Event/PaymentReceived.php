<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\Event;

use Ifx\Account\Domain\Entity\Payment;
use Ifx\Shared\Domain\Event\DomainEvent;
use Ifx\Shared\Domain\ValueObject\AccountId;

final readonly class PaymentReceived extends DomainEvent
{
    public function __construct(
        public AccountId $accountId,
        public Payment $payment,
    ) {
        parent::__construct();
    }
}
