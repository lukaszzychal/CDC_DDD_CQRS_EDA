<?php

declare(strict_types=1);

namespace App\Backend\Infrastructure\Database\Doctrine\Repository;

use App\Backend\Domain\Aggregate\Product;
use App\Backend\Domain\Repository\ProductRepositoryInterface;
use App\Backend\Domain\ValueObject\ProductId;
use App\Backend\Domain\ValueObject\ProductSku;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\MessageBusInterface;

class ProductRepository implements ProductRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Target('event.bus')] private MessageBusInterface $eventBus,
    ) {}

    public function save(Product $product): void
    {
        $this->entityManager->persist($product);
        $this->entityManager->flush();
        $this->dispatchDomainEvents($product);
    }

    public function findProductById(ProductId $productId): ?Product
    {
        return $this->entityManager->find(Product::class, $productId);
    }

    public function findProductBySku(ProductSku $productSku): ?Product
    {
        return $this->entityManager->getRepository(Product::class)->findOneBy([
            'productSku.value' => $productSku->value,
        ]);
    }

    public function remove(Product $product): void
    {
        $this->entityManager->remove($product);
        $this->entityManager->flush();
    }

    public function findAll(): array
    {
        return $this->entityManager->getRepository(Product::class)->findAll();
    }

    private function dispatchDomainEvents(Product $product): void
    {
        foreach ($product->popEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }
}
