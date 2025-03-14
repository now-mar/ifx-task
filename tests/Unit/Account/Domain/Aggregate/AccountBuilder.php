<?php

declare(strict_types=1);

namespace Ifx\Tests\Unit\Account\Domain\Aggregate;

use Ifx\Account\Domain\Aggregate\Account;
use Ifx\Account\Domain\ValueObject\AccountRules;
use Ifx\Shared\Domain\ValueObject\AccountId;
use Ifx\Shared\Domain\ValueObject\Currency;
use Ifx\Shared\Domain\ValueObject\Money;

class AccountBuilder
{
    public $id;
    public $accountRules;
    public $currency;
    public $balance;
    public $receivedPayments;
    public $sentPayments;
    public $fees;
    public $dailyExecutedPaymentsCount;

    public function __construct()
    {
        $this->id = new AccountId();
        $this->accountRules = new AccountRules(3, '0.005');
        $this->currency = new Currency('PLN');
        $this->balance = new Money(1000, $this->currency);
        $this->receivedPayments = [];
        $this->sentPayments = [];
        $this->fees = [];
        $this->dailyExecutedPaymentsCount = [];
    }

    public function getAccount()
    {
        $account = Account::createFromData(
            $this->id,
            $this->currency,
            $this->balance,
            $this->accountRules,
            $this->receivedPayments,
            $this->sentPayments,
            $this->fees,
            $this->dailyExecutedPaymentsCount,
        );

        return $account;
    }
}
