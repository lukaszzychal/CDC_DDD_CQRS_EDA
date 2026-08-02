<?php

declare(strict_types=1);

namespace App\Backend\Domain\ValueObject;

use App\Backend\Domain\Exception\InvalidCategoryIdException;
use Symfony\Component\Uid\Uuid;

final readonly class CategoryId
{
    public string $value;

    public function __construct(string $value)
    {
        if (!Uuid::isValid($value)) {
            throw new InvalidCategoryIdException(sprintf('Invalid Category ID format: "%s".', $value));
        }

        $this->value = $value;
    }

    public static function generate(): self
    {
        return new self(Uuid::v7()->toRfc4122());
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
