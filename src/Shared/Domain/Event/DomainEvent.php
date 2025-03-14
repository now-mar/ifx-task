<?php

declare(strict_types=1);

namespace Ifx\Shared\Domain\Event;

use Ifx\Shared\Domain\ValueObject\EventId;

abstract readonly class DomainEvent
{
    public EventId $id;
    public \DateTimeImmutable $occurredOn;

    public function __construct(?\DateTimeImmutable $occurredOn = null)
    {
        $this->occurredOn = $occurredOn ?: new \DateTimeImmutable();
        $this->id = new EventId();
    }
}
