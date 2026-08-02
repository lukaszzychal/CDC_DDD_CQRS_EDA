<?php

declare(strict_types=1);

namespace App\Backend\Domain\Exception;

use DomainException;

final class InvalidCurrencyException extends DomainException
{
    public function __construct(string $message = 'Currency must be a valid 3-letter ISO 4217 code (e.g. PLN, EUR, USD).')
    {
        parent::__construct($message);
    }
}
