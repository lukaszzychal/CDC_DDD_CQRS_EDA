<?php

declare(strict_types=1);

namespace App\Backend\Domain\Exception;

use DomainException;

final class ProductNotFoundException extends DomainException
{
    public function __construct(string $message = 'Product not found.')
    {
        parent::__construct($message);
    }
}
