<?php

declare(strict_types=1);

namespace App\Backend\Application\Command;

use App\Backend\Domain\Aggregate\Product;
use App\Backend\Domain\Exception\ProductAlreadyExistsException;
use App\Backend\Domain\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateProductCommandHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function __invoke(CreateProductCommand $command): void
    {
        if ($this->productRepository->findProductBySku($command->sku)) {
            throw new ProductAlreadyExistsException(
                sprintf('Product with SKU "%s" already exists.', $command->sku->value)
            );
        }

        $product = Product::create(
            $command->productId,
            $command->name,
            $command->sku,
            $command->price,
            $command->stock,
            $command->categoryId
        );

        $this->productRepository->save($product);
    }
}
