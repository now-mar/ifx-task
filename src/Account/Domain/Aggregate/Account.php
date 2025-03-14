<?php

declare(strict_types=1);

namespace Ifx\Account\Domain\Aggregate;

use Ifx\Account\Domain\Entity\Payment;
use Ifx\Account\Domain\Event\PaymentReceived;
use Ifx\Account\Domain\Event\PaymentSent;
use Ifx\Account\Domain\Service\FeeCalculator;
use Ifx\Account\Domain\Service\TransactionCostsFeeCalculator;
use Ifx\Account\Domain\Specification\DailySentPaymentsLimitSpecification;
use Ifx\Account\Domain\Specification\EnoughBalanceSpecification;
use Ifx\Account\Domain\ValueObject\AccountRules;
use Ifx\Account\Domain\ValueObject\Fee;
use Ifx\Shared\Domain\Event\DomainEvent;
use Ifx\Shared\Domain\Exception\SpecificationException;
use Ifx\Shared\Domain\Specification\CompositeSpecification;
use Ifx\Shared\Domain\Specification\SameCurrencySpecification;
use Ifx\Shared\Domain\ValueObject\AccountId;
use Ifx\Shared\Domain\ValueObject\Currency;
use Ifx\Shared\Domain\ValueObject\Money;

class Account
{
    public readonly AccountId $id;
    public readonly AccountRules $accountRules;
    public readonly Currency $currency;
    public private(set) Money $balance;
    // comment: receivedPayments, sentPayments, and fees could be a LazyCollection instance (custom implementation)
    /** @var Payment[]  */
    public private(set) array $receivedPayments = [];
    /** @var Payment[]  */
    public private(set) array $sentPayments = [];
    /** @var Fee[]  */
    public private(set) array $fees = [];
    public int $todaysExecutedPaymentsCount {
        get => $this->dailyExecutedPaymentsCount[date('Y-m-d')] ?? 0;
    }

    /**
     * @var array<string, int> Where the key is a date in format Y-m-d
     */
    private array $dailyExecutedPaymentsCount = [];

    /**
     * @var DomainEvent[]
     */
    private array $recordedEvents = [];

    protected function recordThat(DomainEvent $domainEvent): void
    {
        match (true) {
            $domainEvent instanceof PaymentReceived => $this->onPaymentReceived($domainEvent),
            $domainEvent instanceof PaymentSent => $this->onPaymentSent($domainEvent),
        };

        $this->recordedEvents[] = $domainEvent;
    }

    public function releaseEvents(): array
    {
        $events = $this->recordedEvents;
        $this->recordedEvents = [];

        return $events;
    }

    private function __construct()
    {
    }

    public static function createFromData(
        AccountId $accountId,
        Currency $accountCurrency,
        Money $balance,
        AccountRules $accountRules,
        /** @param Payment[] $sentPayments */
        array $sentPayments,
        /** @param Payment[] $receivedPayments */
        array $receivedPayments,
        /** @param Fee[] $fees */
        array $fees,
        /** @param array<string, int> $dailyExecutedPaymentsCount */
        array $dailyExecutedPaymentsCount
    ): self {
        $self = new self();
        $self->id = $accountId;
        $self->currency = $accountCurrency;
        $self->balance = $balance;
        $self->accountRules = $accountRules;
        $self->sentPayments = $sentPayments;
        $self->receivedPayments = $receivedPayments;
        $self->fees = $fees;
        $self->dailyExecutedPaymentsCount = $dailyExecutedPaymentsCount;

        return $self;
    }

    public function receivePayment(Payment $payment): void
    {
        $spec = new SameCurrencySpecification($this->currency, $payment->value->currency);

        $specResult = $spec->satisfy();
        if (!$specResult->satisfied) {
            throw new SpecificationException($specResult->errors);
        }

        $this->recordThat(new PaymentReceived($this->id, $payment));
    }

    public function sendPayment(Payment $payment): void
    {
        $currencySpec = new SameCurrencySpecification($this->currency, $payment->value->currency);
        $currSpecResult = $currencySpec->satisfy();
        if (!$currSpecResult->satisfied) {
            throw new SpecificationException($currSpecResult->errors);
        }

        // comment: both fee calculators and specifications could be extracted to external classes
        // if it was required to determine them dynamically, e.g. based on account type or client type.
        // For now we assume every account has the same validation rules
        $calc = new TransactionCostsFeeCalculator($this->accountRules->sendPaymentFee);
        $fee = $calc->calculate($payment);

        $spec = new CompositeSpecification(
            new DailySentPaymentsLimitSpecification($this->todaysExecutedPaymentsCount, $this->accountRules->dailySentPaymentsLimit),
            new EnoughBalanceSpecification($this->balance, $payment->value, $fee->value),
        );

        $specResult = $spec->satisfy();
        if (!$specResult->satisfied) {
            throw new SpecificationException($specResult->errors);
        }

        $this->recordThat(new PaymentSent($this->id, $payment, $fee));
    }

    protected function onPaymentReceived(PaymentReceived $event): void
    {
        $this->balance = $this->balance->add($event->payment->value);
        $this->receivedPayments[] = $event->payment;
    }

    protected function onPaymentSent(PaymentSent $event): void
    {
        $this->balance = $this->balance
            ->sub($event->payment->value)
            ->sub($event->fee->value);

        $today = date('Y-m-d');

        if (!isset($this->dailyExecutedPaymentsCount[$today])) {
            // comment: set new array as there is no need to keep counters from previous days
            $this->dailyExecutedPaymentsCount = [$today => 0];
        }

        $this->dailyExecutedPaymentsCount[$today]++;
        $this->sentPayments[] = $event->payment;
        $this->fees[] = $event->fee;
    }
}
