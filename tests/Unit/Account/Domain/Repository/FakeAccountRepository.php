<?php

declare(strict_types=1);

namespace Ifx\Tests\Unit\Account\Domain\Repository;

use Ifx\Account\Domain\Aggregate\Account;
use Ifx\Account\Domain\Exception\AccountNotFoundException;
use Ifx\Account\Domain\Repository\AccountRepository;
use Ifx\Shared\Domain\ValueObject\AccountId;

final class FakeAccountRepository implements AccountRepository
{
    private array $accounts = [];

    public function save(Account $account): void
    {
        $this->accounts[$account->id->toString()] = $account;
    }

    public function get(AccountId $accountId): Account
    {
        $account = $this->accounts[$accountId->toString()] ?? null;
        if (!$account) {
            throw new AccountNotFoundException($accountId->toString());
        }

        return $account;
    }
}
