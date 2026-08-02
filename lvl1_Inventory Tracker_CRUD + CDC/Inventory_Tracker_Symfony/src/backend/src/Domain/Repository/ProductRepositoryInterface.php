<?php

declare(strict_types=1);

namespace App\Backend\Domain\Repository;

use App\Backend\Domain\Aggregate\Product;
use App\Backend\Domain\ValueObject\ProductId;
use App\Backend\Domain\ValueObject\ProductSku;

interface ProductRepositoryInterface
{
    public function save(Product $product): void;
    public function findProductById(ProductId $productId): ?Product;
    public function findProductBySku(ProductSku $productSku): ?Product;
    public function remove(Product $product): void;
    public function findAll(): array;
}
