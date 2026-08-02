<?php

declare(strict_types=1);

namespace App\Backend\Domain\Aggregate;

use App\Backend\Domain\Event\{ProductWasPriced, ProductCreated};
use App\Backend\Domain\ValueObject\{Money, ProductSku, StockQuantity, ProductId, CategoryId};
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'products')]
final class Product
{
    use AggregateRootTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'product_id', unique: true)]
    private readonly ProductId $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Embedded(class: ProductSku::class)]
    private readonly ProductSku $productSku;

    #[ORM\Embedded(class: Money::class)]
    private Money $price;

    #[ORM\Embedded(class: StockQuantity::class)]
    private StockQuantity $stock;

    #[ORM\Column(type: 'category_id', name: 'category_id')]
    private CategoryId $categoryId;

    public function __construct(
        ProductId $id,
        string $name,
        ProductSku $productSku,
        Money $price,
        StockQuantity $stock,
        CategoryId $categoryId,
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->productSku = $productSku;
        $this->price = $price;
        $this->stock = $stock;
        $this->categoryId = $categoryId;
    }

    public static function create(
        ProductId $id,
        string $name,
        ProductSku $productSku,
        Money $price,
        StockQuantity $stock,
        CategoryId $categoryId,
    ): self {
        $product = new self(
            id: $id,
            name: $name,
            productSku: $productSku,
            price: $price,
            stock: $stock,
            categoryId: $categoryId,
        );

        $product->record(
            new ProductCreated(
                $product->id,
                $product->name,
                $product->productSku,
                $product->price,
                $product->stock,
                $product->categoryId
            )
        );

        return $product;
    }

    public function changePrice(Money $price): void
    {
        if ($price->equals($this->price)) {
            return;
        }
        $oldPrice = $this->price;
        $this->price = $price;

        $this->record(new ProductWasPriced($this->id, $oldPrice, $this->price));
    }

    public function getProductId(): ProductId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getProductSku(): ProductSku
    {
        return $this->productSku;
    }

    public function getPrice(): Money
    {
        return $this->price;
    }

    public function getStock(): StockQuantity
    {
        return $this->stock;
    }

    public function getCategoryId(): CategoryId
    {
        return $this->categoryId;
    }
}
