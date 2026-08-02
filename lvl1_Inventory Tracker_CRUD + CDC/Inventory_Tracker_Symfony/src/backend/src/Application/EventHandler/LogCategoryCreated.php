<?php

declare(strict_types=1);

namespace App\Backend\Application\EventHandler;

use App\Backend\Domain\Event\CategoryCreated;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class LogCategoryCreated
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(CategoryCreated $event): void
    {
        $this->logger->info('Category created', [
            'categoryId' => $event->id->value,
            'name' => $event->name,
        ]);
    }
}
