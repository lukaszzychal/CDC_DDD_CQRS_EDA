<?php

declare(strict_types=1);

namespace App\Backend\Application\EventHandler;

use App\Backend\Domain\Event\ProductCreated;
use App\Backend\Domain\Repository\CategoryRepositoryInterface;
use App\Backend\Infrastructure\Document\ProductReadModelDocument;
use Doctrine\ODM\MongoDB\DocumentManager;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'event.bus')]
final readonly class UpdateMongoDbReadModelOnProductCreated
{
    public function __construct(
        private DocumentManager $documentManager,
        private CategoryRepositoryInterface $categoryRepository,
        private LoggerInterface $logger
    ) {}

    public function __invoke(ProductCreated $event): void
    {
        $category = $this->categoryRepository->findById($event->categoryId);
        $categoryName = $category ? $category->getName() : 'Unknown';

        $document = $this->documentManager->find(ProductReadModelDocument::class, $event->productId->value)
            ?? new ProductReadModelDocument();

        $this->mapEventToDocument($document, $event, $categoryName);

        $this->documentManager->persist($document);
        $this->documentManager->flush();

        $this->logger->info('MongoDB Read Model (Doctrine ODM): Product created/updated from Domain Event', [
            'productId' => $event->productId->value,
            'name' => $event->name,
        ]);
    }

    private function mapEventToDocument(
        ProductReadModelDocument $document,
        ProductCreated $event,
        string $categoryName
    ): void {
        $document->id = $event->productId->value;
        $document->name = $event->name;
        $document->sku = $event->productSku->value;
        $document->price = $event->price->toFloat();
        $document->currency = $event->price->currency;
        $document->stock = $event->stock->value;
        $document->categoryId = $event->categoryId->value;
        $document->categoryName = $categoryName;
        $document->updatedAt = new \DateTimeImmutable();
    }
}
