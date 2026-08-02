<?php

declare(strict_types=1);

namespace App\Backend\Domain\Exception;

use DomainException;

final class PriceCannotBeNegativeException extends DomainException
{
    public function __construct(string $message = 'Price cannot be negative.')
    {
        parent::__construct($message);
    }
}
