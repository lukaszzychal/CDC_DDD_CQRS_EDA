<?php

declare(strict_types=1);

namespace App\Backend\Application\Query;

use Meilisearch\Client;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler(bus: 'query.bus')]
final readonly class SearchProductsMeilisearchQueryHandler
{
    public function __construct(
        private Client $meilisearchClient,
    ) {}

    public function __invoke(SearchProductsMeilisearchQuery $query): array
    {
        $index = $this->meilisearchClient->index('products');
        $searchResult = $index->search($query->searchTerm, [
            'limit' => $query->limit,
        ]);

        return $searchResult->getHits();
    }
}
