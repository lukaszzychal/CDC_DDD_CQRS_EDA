<?php

declare(strict_types=1);

namespace App\Backend\Infrastructure\Database\Doctrine\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use App\Backend\Domain\ValueObject\CategoryId;

class CategoryIdType extends Type
{
    public const NAME = 'category_id';

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
        return $value instanceof CategoryId ? $value->value : $value;
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): mixed
    {
        return $value ? new CategoryId($value) : null;
    }
}
