<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\Exception;

final class AccountNotFoundException extends AccountException
{
    public function __construct(string $accountId)
    {
        parent::__construct('Account with id ' . $accountId . ' not found');
    }
}
