<?php

declare(strict_types=1);

namespace App\Backend\Application\Command;

use App\Backend\Domain\Aggregate\Category;
use App\Backend\Domain\Repository\CategoryRepositoryInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'command.bus')]
final readonly class CreateCategoryCommandHandler
{
    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
    ) {}

    public function __invoke(CreateCategoryCommand $command): void
    {
        $category = Category::create($command->id, $command->name);

        $this->categoryRepository->save($category);
    }
}
