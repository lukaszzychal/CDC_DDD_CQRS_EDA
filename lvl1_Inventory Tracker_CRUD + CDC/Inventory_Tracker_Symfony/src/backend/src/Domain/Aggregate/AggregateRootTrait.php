<?php

declare(strict_types=1);

namespace App\Backend\Domain\Aggregate;

trait AggregateRootTrait
{

    private array $domainEvents = [];
    public function record(object $domainEvent): void
    {
        $this->domainEvents[] = $domainEvent;
    }

    public function popEvents(): array
    {
        $events = $this->domainEvents;
        $this->domainEvents = [];

        return $events;
    }
}
