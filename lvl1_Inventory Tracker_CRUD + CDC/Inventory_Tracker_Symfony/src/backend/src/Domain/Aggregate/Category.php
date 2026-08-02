<?php

declare(strict_types=1);

namespace App\Backend\Domain\Aggregate;

use App\Backend\Domain\Event\{CategoryCreated, CategoryNameChanged};
use App\Backend\Domain\ValueObject\CategoryId;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
class Category
{
    use AggregateRootTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'category_id', unique: true)]
    private CategoryId $id;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    public function __construct(CategoryId $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function create(CategoryId $id, string $name): self
    {
        $category = new self($id, $name);
        $category->record(new CategoryCreated($id, $name));

        return $category;
    }

    public function getId(): CategoryId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function changeName(string $name): void
    {
        $this->name = $name;

        $this->record(
            new CategoryNameChanged($this->id, $name)
        );
    }
}
