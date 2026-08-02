<?php

declare(strict_types=1);

namespace App\Backend\Infrastructure\Database\Doctrine\Repository;

use App\Backend\Domain\Aggregate\Category;
use App\Backend\Domain\Repository\CategoryRepositoryInterface;
use App\Backend\Domain\ValueObject\CategoryId;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Target;
use Symfony\Component\Messenger\MessageBusInterface;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        #[Target('event.bus')] private MessageBusInterface $eventBus,
    ) {}

    public function save(Category $category): void
    {
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        foreach ($category->popEvents() as $event) {
            $this->eventBus->dispatch($event);
        }
    }

    public function findById(CategoryId $id): ?Category
    {
        return $this->entityManager->find(Category::class, $id);
    }
}
