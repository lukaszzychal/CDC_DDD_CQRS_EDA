<?php

declare(strict_types=1);

namespace App\Backend\Domain\ValueObject;

use App\Backend\Domain\Exception\InvalidProductIdException;
use Symfony\Component\Uid\Uuid;

final readonly class ProductId
{
    public function __construct(
        public string $value,
    ) {
        if (!Uuid::isValid($this->value)) {
            throw new InvalidProductIdException('Product ID is not a valid UUID.');
        }
    }

    public function equals(ProductId $other): bool
    {
        return $this->value === $other->value;
    }

    public static function generate(): self
    {
        return new self(Uuid::v7()->toRfc4122());
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
