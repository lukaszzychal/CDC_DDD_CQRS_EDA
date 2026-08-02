<?php

declare(strict_types=1);

namespace App\Backend\Domain\Repository;

use App\Backend\Domain\Aggregate\Category;
use App\Backend\Domain\ValueObject\CategoryId;

interface CategoryRepositoryInterface
{
    public function save(Category $category): void;

    public function findById(CategoryId $id): ?Category;
}
