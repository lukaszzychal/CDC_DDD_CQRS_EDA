<?php

declare(strict_types=1);

namespace App\Backend\Domain\Event;

use App\Backend\Domain\ValueObject\CategoryId;
use App\Backend\Domain\ValueObject\Money;
use App\Backend\Domain\ValueObject\ProductId;
use App\Backend\Domain\ValueObject\ProductSku;
use App\Backend\Domain\ValueObject\StockQuantity;

final readonly class ProductCreated
{
    public function __construct(
        public ProductId $productId,
        public string $name,
        public ProductSku $productSku,
        public Money $price,
        public StockQuantity $stock,
        public CategoryId $categoryId,
    ) {}
}
