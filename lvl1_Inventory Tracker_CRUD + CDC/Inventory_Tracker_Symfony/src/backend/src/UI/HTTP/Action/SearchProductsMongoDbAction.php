<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Action;

use App\Backend\Application\Query\SearchProductsMongoDbQuery;
use App\Backend\UI\HTTP\Responder\SearchProductsMongoDbResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class SearchProductsMongoDbAction
{
    public function __construct(
        private MessageBusInterface $queryBus,
        private SearchProductsMongoDbResponder $responder,
    ) {}

    #[Route('api/products/search/mongodb', name: 'api_search_products_mongodb', methods: ['GET'])]
    public function __invoke(Request $request): JsonResponse
    {
        $searchTerm = (string) $request->query->get('q', '');
        $limit = (int) $request->query->get('limit', 20);

        $query = new SearchProductsMongoDbQuery($searchTerm, $limit);

        $envelope = $this->queryBus->dispatch($query);

        /** @var HandledStamp $handledStamp */
        $handledStamp = $envelope->last(HandledStamp::class);
        $products = $handledStamp ? $handledStamp->getResult() : [];

        return $this->responder->respond($products);
    }
}
