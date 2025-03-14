<?php

declare(strict_types=1);

namespace Ifx\Tests\Unit\Account\Domain\Aggregate;

use Ifx\Account\Domain\Aggregate\Account;
use Ifx\Account\Domain\Entity\Payment;
use Ifx\Account\Domain\Event\PaymentReceived;
use Ifx\Account\Domain\Event\PaymentSent;
use Ifx\Account\Domain\ValueObject\Fee;
use Ifx\Account\Domain\ValueObject\FeeType;
use Ifx\Shared\Domain\Exception\SpecificationException;
use Ifx\Shared\Domain\ValueObject\Currency;
use Ifx\Shared\Domain\ValueObject\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class AccountTest extends TestCase
{
    public function test_send_first_payment()
    {
        $account = new AccountBuilder()->getAccount();
        $initialBalance = $account->balance;
        $feeValue = new Money((int) round(500*0.005), $account->currency);
        $payment = new Payment(new Money(500, $account->currency));
        $account->sendPayment($payment);

        $this->assertEquals($initialBalance->sub($feeValue)->sub($payment->value), $account->balance);
        $this->assertCount(0, $account->receivedPayments);
        $this->assertEquals([$payment], $account->sentPayments);
        $this->assertEquals(new Fee($feeValue, FeeType::TransactionCosts), $account->fees[0]);
    }

    public function test_send_more_payments()
    {
        $account = new AccountBuilder()->getAccount();
        $initialBalance = $account->balance;
        $feeValue = new Money((int) round(100*0.005), $account->currency);
        $payment1 = new Payment(new Money(100, $account->currency));
        $account->sendPayment($payment1);
        $payment2 = new Payment(new Money(100, $account->currency));
        $account->sendPayment($payment2);

        $this->assertEquals(
            $account->balance,
            $initialBalance
                ->sub($payment1->value)
                ->sub($feeValue)
                ->sub($payment2->value)
                ->sub($feeValue)
        );
        $this->assertCount(2, $account->sentPayments);
        $this->assertCount(0, $account->receivedPayments);
        $this->assertSame([$payment1, $payment2], $account->sentPayments);
        $this->assertEquals([new Fee($feeValue, FeeType::TransactionCosts), new Fee($feeValue, FeeType::TransactionCosts)], $account->fees);
    }

    #[DataProvider('send_payment_fails_on_specification_error_dataprovider')]
    public function test_send_payment_fails_on_specification_error(Account $account, Payment $payment, array $errors)
    {
        try {
            $account->sendPayment($payment);
        } catch (SpecificationException $e) {
            $this->assertEquals($errors, $e->errors);
        }
    }

    public function test_receive_payment()
    {
        $account = new AccountBuilder()->getAccount();
        $initialBalance = $account->balance;
        $payment = new Payment(new Money(500, $account->currency));
        $account->receivePayment($payment);

        $this->assertEquals($initialBalance->add($payment->value), $account->balance);
        $this->assertCount(1, $account->receivedPayments);
        $this->assertCount(0, $account->sentPayments);
        $this->assertCount(0, $account->fees);
        $this->assertEquals([$payment], $account->receivedPayments);
    }

    public function test_events_recorded()
    {
        $account = new AccountBuilder()->getAccount();
        $sentPayment = new Payment(new Money(200, $account->currency));
        $receivedPayment = new Payment(new Money(100, $account->currency));
        $account->sendPayment($sentPayment);
        $account->receivePayment($receivedPayment);

        $events = $account->releaseEvents();
        /** @var PaymentSent $paymentSentEvent */
        $paymentSentEvent = $events[0];

        $this->assertEquals($account->id, $paymentSentEvent->accountId);
        $this->assertEquals($sentPayment, $paymentSentEvent->payment);
        $this->assertInstanceOf(Fee::class, $paymentSentEvent->fee);

        /** @var PaymentReceived $paymentSentEvent */
        $paymentReceivedEvent = $events[1];

        $this->assertEquals($account->id, $paymentReceivedEvent->accountId);
        $this->assertEquals($receivedPayment, $paymentReceivedEvent->payment);
    }

    public static function send_payment_fails_on_specification_error_dataprovider()
    {
        yield 'payment value higher than balance' => [
            new AccountBuilder()->getAccount(),
            new Payment(new Money(2000, new Currency('PLN'))),
            ['Not enough balance']
        ];

        yield 'payment value lower than balance but together with fee addition higher than balance' => [
            new AccountBuilder()->getAccount(),
            new Payment(new Money(999, new Currency('PLN'))),
            ['Not enough balance']
        ];

        $accBuilder = new AccountBuilder();
        $accBuilder->dailyExecutedPaymentsCount = [date('Y-m-d') => 3];
        $account1 = $accBuilder->getAccount();
        yield 'daily sent payments limit exceeded' => [
            $account1,
            new Payment(new Money(500, new Currency('PLN'))),
            ['Daily sent payments limit exceeded']
        ];

        yield 'payment and account currency mismatch' => [
            new AccountBuilder()->getAccount(),
            new Payment(new Money(500, new Currency('EUR'))),
            ['Currency mismatch']
        ];

        yield 'currency mismatch AND not enough balance. Should first and only check same currency.' => [
            new AccountBuilder()->getAccount(),
            new Payment(new Money(999, new Currency('EUR'))),
            ['Currency mismatch']
        ];
    }
}
