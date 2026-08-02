<?php

declare(strict_types=1);

namespace App\Backend\Infrastructure\Database\Doctrine\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use App\Backend\Domain\ValueObject\ProductId;

class ProductIdType  extends Type
{
    public const NAME = 'product_id';

    public function getName(): string
    {
        return self::NAME;
    }

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return $platform->getGuidTypeDeclarationSQL($column);
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform): mixed
    {
        return $value instanceof ProductId ? $value->value : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return $value ? new ProductId($value) : null;
    }
}
