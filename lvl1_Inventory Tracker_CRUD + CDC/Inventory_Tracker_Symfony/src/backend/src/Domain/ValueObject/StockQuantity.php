<?php

declare(strict_types=1);

namespace App\Backend\Domain\ValueObject;

use App\Backend\Domain\Exception\InvalidStockQuantityException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class StockQuantity
{
    #[ORM\Column(type: 'integer', name: 'stock_quantity')]
    public int $value;

    public function __construct(
        int $value,
    ) {
        if ($value < 0) {
            throw new InvalidStockQuantityException('Stock quantity cannot be negative.');
        }

        $this->value = $value;
    }

    public static function create(int $value): self
    {
        return new self($value);
    }

    public function add(int $amount): self
    {
        return new self($this->value + $amount);
    }

    public function decreaseStock(int $amount): self
    {
        return new self($this->value - $amount);
    }

    public function isOutOfStock(): bool
    {
        return $this->value === 0;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public static function fromInt(int $value): self
    {
        return new self($value);
    }
}
