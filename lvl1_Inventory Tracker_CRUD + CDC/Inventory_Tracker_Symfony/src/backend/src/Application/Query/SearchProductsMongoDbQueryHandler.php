<?php

declare(strict_types=1);

namespace App\Backend\Application\Query;

use App\Backend\Infrastructure\Document\ProductReadModelDocument;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class SearchProductsMongoDbQueryHandler
{
    public function __construct(
        private DocumentManager $documentManager,
    ) {}

    public function __invoke(SearchProductsMongoDbQuery $query): array
    {
        $qb = $this->documentManager->createQueryBuilder(ProductReadModelDocument::class);

        if (!empty($query->searchTerm)) {
            $regex = new \MongoDB\BSON\Regex($query->searchTerm, 'i');
            $qb->addOr($qb->expr()->field('name')->equals($regex))
               ->addOr($qb->expr()->field('sku')->equals($regex))
               ->addOr($qb->expr()->field('categoryName')->equals($regex));
        }

        $qb->limit($query->limit)
           ->sort('updatedAt', 'DESC');

        $documents = $qb->getQuery()->execute();

        $results = [];
        /** @var ProductReadModelDocument $doc */
        foreach ($documents as $doc) {
            $results[] = $doc->toArray();
        }

        return $results;
    }
}
