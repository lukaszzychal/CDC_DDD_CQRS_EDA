<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Responder;

use App\Backend\Domain\ValueObject\CategoryId;
use App\Backend\UI\HTTP\Response\JsonResponseFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class CreateCategoryResponder
{
    public function __construct(
        private JsonResponseFactoryInterface $responseFactory,
    ) {}

    public function created(CategoryId $categoryId): JsonResponse
    {
        return $this->responseFactory->createSuccessResponse(
            'Category created successfully',
            Response::HTTP_CREATED,
            ['categoryId' => $categoryId->value],
        );
    }
}
