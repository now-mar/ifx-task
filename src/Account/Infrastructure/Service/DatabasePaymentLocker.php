<?php

declare(strict_types=1);

namespace Ifx\Account\Infrastructure\Service;

use Ifx\Account\Application\Service\PaymentLocker;
use Ifx\Shared\Domain\ValueObject\AccountId;

// comment: pseudocode
final class DatabasePaymentLocker implements PaymentLocker
{
    public function lock(AccountId $accountId): void
    {
        try {
            $this->database->table('locks')->insert([
                'type' => 'payment',
                'identifier' => $accountId->toString()
            ]);
        } catch (ConstraintViolation $e) {
            throw new AnotherPaymentInProgress();
        }
    }

    public function unlock(AccountId $accountId): void
    {
        $this->database->table('locks')->delete([
            'type' => 'payment',
            'identifier' => $accountId->toString()
        ]);
    }
}
