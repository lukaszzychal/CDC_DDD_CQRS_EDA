<?php

declare(strict_types=1);

namespace App\Backend\Application\Command;

use App\Backend\Domain\Exception\ProductNotFoundException;
use App\Backend\Domain\Repository\ProductRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class ChangeProductPriceCommandHandler
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function __invoke(ChangeProductPriceCommand $command): void
    {
        $product = $this->productRepository->findProductById($command->productId);

        if (!$product) {
            throw new ProductNotFoundException(
                sprintf('Product with ID "%s" not found.', $command->productId->value)
            );
        }

        $product->changePrice($command->newPrice);

        $this->productRepository->save($product);
    }
}
