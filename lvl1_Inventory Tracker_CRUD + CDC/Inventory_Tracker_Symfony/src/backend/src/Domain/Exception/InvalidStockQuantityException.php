<?php

declare(strict_types=1);

namespace App\Backend\Domain\Exception;

use DomainException;

final class InvalidStockQuantityException extends DomainException
{
    public function __construct(string $message = 'Stock quantity cannot be negative.')
    {
        parent::__construct($message);
    }
}
