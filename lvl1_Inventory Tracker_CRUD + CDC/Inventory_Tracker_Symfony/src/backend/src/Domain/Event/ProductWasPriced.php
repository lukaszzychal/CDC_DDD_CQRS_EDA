<?php

declare(strict_types=1);

namespace App\Backend\Domain\Event;

use App\Backend\Domain\ValueObject\Money;
use App\Backend\Domain\ValueObject\ProductId;
use DateTimeImmutable;

final readonly class ProductWasPriced
{
    public function __construct(
        public ProductId $productId,
        public Money $oldPrice,
        public Money $newPrice,
        public DateTimeImmutable $occurredOn = new DateTimeImmutable()
    ) {}
}
