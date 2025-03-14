<?php

declare(strict_types=1);

namespace Ifx\Tests\Unit\Account\Domain\Service;

use Ifx\Account\Application\Service\PaymentService;
use Ifx\Account\Domain\Aggregate\Account;
use Ifx\Account\Domain\Entity\Payment;
use Ifx\Shared\Domain\ValueObject\AccountId;
use Ifx\Shared\Domain\ValueObject\Currency;
use Ifx\Shared\Domain\ValueObject\Money;
use Ifx\Tests\Unit\Account\Domain\Repository\FakeAccountRepository;
use PHPUnit\Framework\Constraint\Callback;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

final class PaymentServiceTest extends TestCase
{
    public function test_send_payment()
    {
        $pln = new Currency('PLN');
        $paymentValue = new Money(1000, $pln);
        $account = $this->createPartialMock(Account::class, ['sendPayment']);
        $ref = new ReflectionProperty($account, 'id');
        $ref->setValue($account, $accountId = new AccountId());

        $account->expects($this->once())->method('sendPayment')->with(
            new Callback(function (Payment $payment) use ($paymentValue) {
                $this->assertEquals($paymentValue, $payment->value);

                return true;
            })
        );

        $accountRepository = new FakeAccountRepository();
        $accountRepository->save($account);
        $paymentService = new PaymentService($accountRepository);
        $paymentService->sendPayment($accountId->toString(), $paymentValue);
    }

    public function test_receive_payment()
    {
        $pln = new Currency('PLN');
        $paymentValue = new Money(1000, $pln);
        $account = $this->createPartialMock(Account::class, ['receivePayment']);
        $ref = new ReflectionProperty($account, 'id');
        $ref->setValue($account, $accountId = new AccountId());

        $account->expects($this->once())->method('receivePayment')->with(
            new Callback(function (Payment $payment) use ($paymentValue) {
                $this->assertEquals($paymentValue, $payment->value);

                return true;
            })
        );

        $accountRepository = new FakeAccountRepository();
        $accountRepository->save($account);
        $paymentService = new PaymentService($accountRepository);
        $paymentService->receivePayment($accountId->toString(), $paymentValue);
    }
}
