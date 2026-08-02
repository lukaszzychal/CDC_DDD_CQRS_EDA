<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Responder;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class SearchProductsMongoDbResponder
{
    public function respond(array $products): JsonResponse
    {
        return new JsonResponse([
            'status' => 'success',
            'read_model' => 'MongoDB (Doctrine ODM - In-Process Domain Events Projection)',
            'message' => 'Products retrieved successfully from MongoDB Read Model via Doctrine ODM',
            'data' => $products,
        ], Response::HTTP_OK);
    }
}
