<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateProductRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Product name cannot be empty.')]
        public string $name,

        #[Assert\NotBlank(message: 'SKU cannot be empty.')]
        #[Assert\Regex(pattern: '/^[A-Z0-9]{5,15}$/', message: 'SKU must consist of 5-15 uppercase alphanumeric characters.')]
        public string $sku,

        #[Assert\NotNull]
        #[Assert\GreaterThanOrEqual(0, message: 'Price cannot be negative.')]
        public float $price,

        #[Assert\NotBlank]
        #[Assert\Length(exactly: 3, exactMessage: 'Currency must be a 3-letter ISO code.')]
        public string $currency,

        #[Assert\NotBlank(message: 'Category ID cannot be empty.')]
        #[Assert\Uuid(message: 'Category ID must be a valid UUID.')]
        public string $categoryId,

        #[Assert\NotBlank]
        #[Assert\GreaterThanOrEqual(0, message: 'Stock must be non-negative.')]
        #[Assert\Type(type: 'integer', message: 'Stock must be an integer.')]
        public int $stock = 0,
    ) {}
}
