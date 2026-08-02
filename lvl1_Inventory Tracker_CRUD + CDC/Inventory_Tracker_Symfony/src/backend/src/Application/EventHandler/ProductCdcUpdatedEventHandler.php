<?php

declare(strict_types=1);

namespace App\Backend\Application\EventHandler;

use App\Backend\Application\Event\ProductCdcUpdatedEvent;
use App\Backend\Domain\Repository\CategoryRepositoryInterface;
use App\Backend\Domain\ValueObject\CategoryId;
use Meilisearch\Client as MeilisearchClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class ProductCdcUpdatedEventHandler
{
    public function __construct(
        private LoggerInterface $logger,
        private CategoryRepositoryInterface $categoryRepository,
        private MeilisearchClient $meilisearchClient,
    ) {}

    public function __invoke(ProductCdcUpdatedEvent $event): void
    {
        if ($event->isCreate() || $event->isUpdate()) {
            $after = $event->after;
            if (!$after) {
                return;
            }

            $document = $this->buildMeilisearchDocument($after);

            if ($event->isCreate()) {
                $this->logger->info('CDC Event: New product creation detected in PostgreSQL', $document);
                $this->meilisearchClient->index('products')->addDocuments([$document], 'id');
            } else {
                if ($event->hasPriceChanged()) {
                    $rawOldPrice = $event->before['price_price_amount'] ?? $event->before['price_amount'] ?? null;
                    $oldPrice = $rawOldPrice !== null ? (float) $rawOldPrice / 100 : null;

                    $this->logger->info('CDC Event: Product price change detected in PostgreSQL', [
                        'id' => $document['id'],
                        'oldPrice' => $oldPrice,
                        'newPrice' => $document['price'],
                        'currency' => $document['currency'],
                    ]);
                } else {
                    $this->logger->info('CDC Event: Product update detected in PostgreSQL', $document);
                }

                $this->meilisearchClient->index('products')->updateDocuments([$document], 'id');
            }
        }

        if ($event->isDelete()) {
            $productId = $event->before['id'] ?? null;

            $this->logger->info('CDC Event: Product deletion detected from PostgreSQL', [
                'id' => $productId,
            ]);

            if ($productId) {
                $this->meilisearchClient->index('products')->deleteDocument($productId);
            }
        }
    }

    private function buildMeilisearchDocument(array $after): array
    {
        $categoryName = null;
        if (!empty($after['category_id'])) {
            $category = $this->categoryRepository->findById(new CategoryId($after['category_id']));
            $categoryName = $category?->getName();
        }

        $sku = $after['product_sku_product_sku'] ?? $after['product_sku'] ?? $after['sku'] ?? null;

        $rawPrice = $after['price_price_amount'] ?? $after['price_amount'] ?? null;
        $price = $rawPrice !== null ? (float) $rawPrice / 100 : null;

        $currency = $after['price_price_currency'] ?? $after['price_currency'] ?? 'PLN';

        $stock = $after['stock_stock_quantity'] ?? $after['stock_quantity'] ?? 0;

        return [
            'id' => $after['id'] ?? null,
            'name' => $after['name'] ?? null,
            'sku' => $sku,
            'price' => $price,
            'currency' => $currency,
            'stock' => (int) $stock,
            'category_id' => $after['category_id'] ?? null,
            'category_name' => $categoryName,
        ];
    }
}
