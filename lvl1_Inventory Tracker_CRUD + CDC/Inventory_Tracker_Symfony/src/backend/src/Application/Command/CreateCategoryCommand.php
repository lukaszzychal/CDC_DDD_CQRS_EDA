<?php

declare(strict_types=1);

namespace App\Backend\Application\Command;

use App\Backend\Domain\ValueObject\CategoryId;
use App\Backend\UI\HTTP\Request\CreateCategoryRequest;

final readonly class CreateCategoryCommand
{
    public function __construct(
        public CategoryId $id,
        public string $name,
    ) {}

    public static function fromRequest(CreateCategoryRequest $request): self
    {
        return new self(
            CategoryId::generate(),
            $request->name
        );
    }
}
