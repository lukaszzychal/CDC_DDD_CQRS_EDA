<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Action;

use App\Backend\Application\Command\CreateProductCommand;
use App\Backend\Domain\ValueObject\ProductId;
use App\Backend\UI\HTTP\Request\CreateProductRequest;
use App\Backend\UI\HTTP\Responder\CreateProductResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class CreateProductAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private CreateProductResponder $responder
    ) {}

    #[Route("api/product", name: "api_create_product", methods: ["POST"])]
    public function __invoke(
        #[MapRequestPayload] CreateProductRequest $createProductRequest
    ): JsonResponse {

        $command = CreateProductCommand::fromRequest($createProductRequest);
        $this->commandBus->dispatch($command);

        return $this->responder->created($command->productId);
    }
}
