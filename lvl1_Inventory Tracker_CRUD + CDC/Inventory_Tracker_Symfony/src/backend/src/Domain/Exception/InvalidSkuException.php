<?php

declare(strict_types=1);

namespace App\Backend\Domain\Exception;

use DomainException;

final class InvalidSkuException extends DomainException
{
    public function __construct(string $message = 'SKU must consist of 5-15 uppercase letters and numbers.')
    {
        parent::__construct($message);
    }
}
