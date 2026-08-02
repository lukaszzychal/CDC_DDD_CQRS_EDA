<?php

declare(strict_types=1);

namespace App\Backend\Domain\Exception;

use DomainException;

final class InvalidCategoryIdException extends DomainException
{
    public function __construct(string $message = 'Invalid category ID.')
    {
        parent::__construct($message);
    }
}
