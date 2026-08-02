<?php

declare(strict_types=1);

namespace App\Backend\Domain\Enum;

enum CdcOperation: string
{
    case CREATE = 'c';
    case UPDATE = 'u';
    case DELETE = 'd';
    case READ = 'r';

    public function isCreate(): bool
    {
        return $this === self::CREATE || $this === self::READ;
    }

    public function isUpdate(): bool
    {
        return $this === self::UPDATE;
    }

    public function isDelete(): bool
    {
        return $this === self::DELETE;
    }
}
