<?php

declare(strict_types=1);

namespace App\Backend\Domain\ValueObject;

use App\Backend\Domain\Exception\InvalidSkuException;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
final readonly class ProductSku
{
    #[ORM\Column(type: 'string', length: 255, name: 'product_sku')]
    public string $value;

    public function __construct(
        string $value,
    ) {
        if (!preg_match('/^[A-Z0-9]{5,15}$/', $value)) {
            throw new InvalidSkuException('SKU must consist of 5-15 uppercase letters and numbers.');
        }

        $this->value = $value;
    }

    public function equals(ProductSku $other): bool
    {
        return $this->value === $other->value;
    }

    public static function generate(): self
    {
        $length = rand(5, 15);
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $randomString = '';
        $charactersLength = strlen($characters);
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return new self($randomString);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
