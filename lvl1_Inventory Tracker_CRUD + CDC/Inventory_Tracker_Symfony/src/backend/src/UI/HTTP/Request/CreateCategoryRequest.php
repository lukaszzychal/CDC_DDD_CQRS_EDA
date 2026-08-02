<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Request;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateCategoryRequest
{
    public function __construct(
        #[Assert\NotBlank(message: 'Category name cannot be empty.')]
        #[Assert\Length(min: 2, max: 100)]
        public string $name,
    ) {}
}
