<?php

declare(strict_types=1);

namespace App\Backend\Domain\ValueObject;

use App\Backend\Domain\Exception\InvalidCurrencyException;
use App\Backend\Domain\Exception\PriceCannotBeNegativeException;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

#[ORM\Embeddable]
final readonly class Money
{
    #[ORM\Column(type: 'integer', name: 'price_amount')]
    public int $amount;

    #[ORM\Column(type: 'string', length: 3, name: 'price_currency')]
    public string $currency;

    public function __construct(
        int $amount,
        string $currency = 'PLN',
    ) {
        if ($amount < 0) {
            throw new PriceCannotBeNegativeException();
        }

        if (strlen(trim($currency)) !== 3) {
            throw new InvalidCurrencyException();
        }

        $this->amount = $amount;
        $this->currency = strtoupper($currency);
    }

    public static function create(int $amount, string $currency = 'PLN'): self
    {
        return new self($amount, $currency);
    }

    public static function fromFloat(float $amount, string $currency = 'PLN'): self
    {
        return new self((int) round($amount * 100), $currency);
    }

    public function toFloat(): float
    {
        return $this->amount / 100;
    }

    public function add(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount
            && strtoupper($this->currency) === strtoupper($other->currency);
    }

    private function assertSameCurrency(self $other): void
    {
        if (strtoupper($this->currency) !== strtoupper($other->currency)) {
            throw new InvalidArgumentException(
                sprintf('Cannot perform operation on different currencies: %s and %s', $this->currency, $other->currency)
            );
        }
    }
}
