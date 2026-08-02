<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Responder;

use App\Backend\Domain\ValueObject\ProductId;
use App\Backend\UI\HTTP\Response\JsonResponseFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

final readonly class CreateProductResponder
{

    public function __construct(
        private JsonResponseFactoryInterface $responseFactory,
    ) {}

    public function created(ProductId $productId): JsonResponse
    {
        return $this->responseFactory->createSuccessResponse(
            'Product created successfully',
            Response::HTTP_CREATED,
            ['productId' => $productId->value],
        );
    }
}
