<?php

declare(strict_types=1);

namespace App\Backend\Application\Command;

use App\Backend\Domain\ValueObject\CategoryId;
use App\Backend\Domain\ValueObject\Money;
use App\Backend\Domain\ValueObject\ProductId;
use App\Backend\Domain\ValueObject\ProductSku;
use App\Backend\Domain\ValueObject\StockQuantity;
use App\Backend\UI\HTTP\Request\CreateProductRequest;

final readonly class CreateProductCommand
{
    public function __construct(
        public ProductId $productId,
        public string $name,
        public ProductSku $sku,
        public Money $price,
        public StockQuantity $stock,
        public CategoryId $categoryId,
    ) {}

    public static function fromRequest(CreateProductRequest $createProductRequest): self
    {
        return new self(
            ProductId::generate(),
            $createProductRequest->name,
            ProductSku::fromString($createProductRequest->sku),
            Money::fromFloat($createProductRequest->price, $createProductRequest->currency),
            StockQuantity::fromInt($createProductRequest->stock),
            new CategoryId($createProductRequest->categoryId)
        );
    }
}
