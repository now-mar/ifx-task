<?php

declare(strict_types=1);

namespace Ifx\Account\Application\Service;

use Ifx\Shared\Domain\ValueObject\AccountId;

interface PaymentLocker
{
    public function lock(AccountId $accountId): void;

    public function unlock(AccountId $accountId): void;
}
