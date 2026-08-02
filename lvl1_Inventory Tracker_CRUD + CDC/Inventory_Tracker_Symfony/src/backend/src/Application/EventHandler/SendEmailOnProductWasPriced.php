<?php

declare(strict_types=1);

namespace App\Backend\Application\EventHandler;

use App\Backend\Domain\Event\ProductWasPriced;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class SendEmailOnProductWasPriced
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ProductWasPriced $event): void
    {
        $this->logger->info('Sending email about product price change');
    }
}
