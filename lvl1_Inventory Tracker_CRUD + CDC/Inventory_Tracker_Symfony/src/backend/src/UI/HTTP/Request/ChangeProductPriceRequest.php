<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class ChangeProductPriceRequest
{
    public function __construct(
        #[Assert\NotNull]
        #[Assert\GreaterThanOrEqual(0)]
        public float $price,

        #[Assert\NotBlank]
        #[Assert\Length(exactly: 3)]
        public string $currency = 'PLN',
    ) {}
}
