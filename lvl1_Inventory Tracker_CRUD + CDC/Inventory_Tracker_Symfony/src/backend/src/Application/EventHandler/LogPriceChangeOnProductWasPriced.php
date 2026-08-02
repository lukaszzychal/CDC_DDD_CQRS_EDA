<?php

declare(strict_types=1);

namespace App\Backend\Application\EventHandler;

use App\Backend\Domain\Event\ProductWasPriced;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class LogPriceChangeOnProductWasPriced
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ProductWasPriced $event): void
    {
        $this->logger->info('Product price changed', [
            'productId' => $event->productId->value,
            'oldPrice' => $event->oldPrice->toFloat(),
            'newPrice' => $event->newPrice->toFloat(),
            'occurredOn' => $event->occurredOn->format('Y-m-d H:i:s'),
        ]);
    }
}
