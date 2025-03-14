<?php

declare(strict_types=1);

namespace Ifx\Account\Application\Service;

use Ifx\Account\Domain\Entity\Payment;
use Ifx\Account\Domain\Repository\AccountRepository;
use Ifx\Shared\Domain\ValueObject\AccountId;
use Ifx\Shared\Domain\ValueObject\Money;

final class PaymentService
{
    public function __construct(
        private AccountRepository $accountRepository,
    ) {
    }

    public function sendPayment(string $accountId, Money $amount): void
    {
        $accountId = AccountId::fromString($accountId);

        // comment: we should lock an account for single payment at the time.
        // Locking account for payment could be also implemented as a Middleware or some kind of callable decorator for PaymentService
        // $this->paymentLocker->lock($accountId);

        $account = $this->accountRepository->get($accountId);
        $payment = new Payment($amount);

        // comment: start transaction. We want the process to be atomic.
        // Transaction could be also implemented as a Middleware or some kind of callable decorator for PaymentService
        $account->sendPayment($payment);
        $this->accountRepository->save($account);
        // comment: commit transaction or rollback

        // comment: unlock account
        // $this->paymentLocker->unlock($accountId);

        $events = $account->releaseEvents();
        // comment: publish events to the EventBus?
    }

    public function receivePayment(string $accountId, Money $amount): void
    {
        $accountId = AccountId::fromString($accountId);

        // comment: same comments as in sendPayment() method

        // $this->paymentLocker->lock($accountId);

        $account = $this->accountRepository->get($accountId);
        $payment = new Payment($amount);

        $account->receivePayment($payment);
        $this->accountRepository->save($account);

        // $this->paymentLocker->unlock($accountId);

        $events = $account->releaseEvents();
    }
}
