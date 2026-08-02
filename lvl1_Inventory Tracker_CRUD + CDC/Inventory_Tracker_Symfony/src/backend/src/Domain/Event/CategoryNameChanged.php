<?php

declare(strict_types=1);

namespace App\Backend\Domain\Event;

use App\Backend\Domain\ValueObject\CategoryId;

final readonly class CategoryNameChanged
{
    public function __construct(
        public CategoryId $id,
        public string $name,
    ) {}
}
