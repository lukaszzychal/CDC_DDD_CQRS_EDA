<?php

declare(strict_types=1);

namespace App\Backend\Application\EventHandler;

use App\Backend\Domain\Event\ProductWasPriced;
use App\Backend\Infrastructure\Document\ProductReadModelDocument;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class UpdateMongoDbReadModelOnProductWasPriced
{
    public function __construct(
        private DocumentManager $documentManager,
        private LoggerInterface $logger
    ) {}

    public function __invoke(ProductWasPriced $event): void
    {
        $document = $this->documentManager->find(ProductReadModelDocument::class, $event->productId->value);

        if ($document instanceof ProductReadModelDocument) {
            $document->price = $event->newPrice->toFloat();
            $document->currency = $event->newPrice->currency;
            $document->updatedAt = new \DateTimeImmutable();

            $this->documentManager->flush();

            $this->logger->info('MongoDB Read Model (Doctrine ODM): Product price updated from Domain Event', [
                'productId' => $event->productId->value,
                'newPrice' => $event->newPrice->toFloat(),
            ]);
        }
    }
}
