<?php

declare(strict_types=1);

namespace App\Backend\Infrastructure\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;

#[ODM\Document(collection: 'products_read_model')]
class ProductReadModelDocument
{
    #[ODM\Id(type: 'string', strategy: 'NONE')]
    public string $id;

    #[ODM\Field(type: 'string')]
    public string $name;

    #[ODM\Field(type: 'string')]
    public string $sku;

    #[ODM\Field(type: 'float')]
    public float $price;

    #[ODM\Field(type: 'string')]
    public string $currency;

    #[ODM\Field(type: 'int')]
    public int $stock;

    #[ODM\Field(type: 'string', name: 'category_id')]
    public string $categoryId;

    #[ODM\Field(type: 'string', name: 'category_name')]
    public string $categoryName;

    #[ODM\Field(type: 'date_immutable', name: 'updated_at')]
    public \DateTimeImmutable $updatedAt;

    public function toArray(): array
    {
        return [
            '_id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'price' => $this->price,
            'currency' => $this->currency,
            'stock' => $this->stock,
            'category_id' => $this->categoryId,
            'category_name' => $this->categoryName,
            'updated_at' => $this->updatedAt->format(\DateTimeInterface::ATOM),
        ];
    }
}
