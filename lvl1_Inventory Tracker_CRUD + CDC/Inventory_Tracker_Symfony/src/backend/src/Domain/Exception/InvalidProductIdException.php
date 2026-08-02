<?php

declare(strict_types=1);

namespace App\Backend\Domain\Exception;

use DomainException;

final class InvalidProductIdException extends DomainException
{
    public function __construct(string $message = 'Product ID is not a valid UUID.')
    {
        parent::__construct($message);
    }
}
