<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class SearchProductsMeilisearchResponder
{
    public function respond(array $products): JsonResponse
    {
        return new JsonResponse([
            'status' => 'success',
            'read_model' => 'Meilisearch (Out-of-Process CDC via Debezium + Kafka)',
            'message' => 'Products retrieved successfully from Meilisearch',
            'data' => $products,
        ], Response::HTTP_OK);
    }
}
