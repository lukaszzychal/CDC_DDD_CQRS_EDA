<?php

declare(strict_types=1);

namespace App\Backend\Application\Query;

final readonly class SearchProductsMeilisearchQuery
{
    public function __construct(
        public string $searchTerm,
        public int $limit = 20,
    ) {}
}
