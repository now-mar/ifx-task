<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\Repository;

use Ifx\Account\Domain\Aggregate\Account;
use Ifx\Account\Domain\Exception\AccountNotFoundException;
use Ifx\Shared\Domain\ValueObject\AccountId;

interface AccountRepository
{
    public function save(Account $account): void;

    /**
     * @param AccountId $accountId
     *
     * @throws AccountNotFoundException When account not found
     */
    public function get(AccountId $accountId): Account;
}
