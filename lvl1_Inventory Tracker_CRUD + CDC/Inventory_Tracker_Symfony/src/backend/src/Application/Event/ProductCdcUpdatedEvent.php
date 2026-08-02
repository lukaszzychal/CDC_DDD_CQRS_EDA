<?php

declare(strict_types=1);

namespace App\Backend\Application\Event;

use App\Backend\Domain\Enum\CdcOperation;

final readonly class ProductCdcUpdatedEvent
{
    public function __construct(
        public CdcOperation $op,
        public ?array $before,
        public ?array $after,
        public int $timestamp,
    ) {}

    public function isCreate(): bool
    {
        return $this->op->isCreate();
    }

    public function isUpdate(): bool
    {
        return $this->op->isUpdate();
    }

    public function isDelete(): bool
    {
        return $this->op->isDelete();
    }

    public function hasPriceChanged(): bool
    {
        if (!$this->isUpdate() || !$this->before || !$this->after) {
            return false;
        }

        $oldPrice = $this->before['price_price_amount'] ?? $this->before['price_amount'] ?? null;
        $newPrice = $this->after['price_price_amount'] ?? $this->after['price_amount'] ?? null;

        $oldCurrency = $this->before['price_price_currency'] ?? $this->before['price_currency'] ?? null;
        $newCurrency = $this->after['price_price_currency'] ?? $this->after['price_currency'] ?? null;

        return $oldPrice !== $newPrice || $oldCurrency !== $newCurrency;
    }
}
