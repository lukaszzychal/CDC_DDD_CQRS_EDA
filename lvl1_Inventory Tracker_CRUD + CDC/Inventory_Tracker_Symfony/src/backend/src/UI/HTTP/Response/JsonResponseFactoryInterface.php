<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Response;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

interface JsonResponseFactoryInterface
{
    public function createSuccessResponse(
        string $message,
        int $statusCode = Response::HTTP_OK,
        array $data = []
    ): JsonResponse;
}
