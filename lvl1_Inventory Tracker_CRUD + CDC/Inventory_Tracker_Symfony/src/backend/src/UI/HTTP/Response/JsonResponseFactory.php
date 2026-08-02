<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class JsonResponseFactory implements JsonResponseFactoryInterface
{
    public function createSuccessResponse(
        string $message,
        int $statusCode = Response::HTTP_OK,
        array $data = []
    ): JsonResponse {
        return new JsonResponse([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }
}
