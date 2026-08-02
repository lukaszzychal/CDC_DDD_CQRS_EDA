<?php

declare(strict_types=1);

namespace App\Backend\Application\EventHandler;

use App\Backend\Domain\Event\ProductCreated;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class LogPriceCreateProduct
{
    public function __construct(
        private LoggerInterface $logger,
    ) {}

    public function __invoke(ProductCreated $event): void
    {
        $this->logger->info('Product created', [
            'productId' => $event->productId->value,
            'name' => $event->name,
            'productSku' => $event->productSku->value,
            'price' => $event->price->amount,
            'currency' => $event->price->currency,
            'stock' => $event->stock->value,
            'categoryId' => $event->categoryId->value,
        ]);
    }
}
