<?php

declare(strict_types=1);

namespace App\Backend\Domain\Exception;

use DomainException;

final class ProductAlreadyExistsException extends DomainException
{
    public function __construct(string $message = 'Product with this SKU already exists.')
    {
        parent::__construct($message);
    }
}
