<?php

declare(strict_types=1);

namespace App\Backend\Application\Command;

use App\Backend\Domain\ValueObject\Money;
use App\Backend\Domain\ValueObject\ProductId;
use App\Backend\UI\HTTP\Request\ChangeProductPriceRequest;

final readonly class ChangeProductPriceCommand
{
    public function __construct(
        public ProductId $productId,
        public Money $newPrice,
    ) {}

    public static function fromRequest(string $productId, ChangeProductPriceRequest $request): self
    {
        return new self(
            new ProductId($productId),
            Money::fromFloat($request->price, $request->currency)
        );
    }
}
