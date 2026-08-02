<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Action;

use App\Backend\Application\Command\ChangeProductPriceCommand;
use App\Backend\UI\HTTP\Request\ChangeProductPriceRequest;
use App\Backend\UI\HTTP\Responder\ChangeProductPriceResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class ChangeProductPriceAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private ChangeProductPriceResponder $responder,
    ) {}

    #[Route('api/product/{productId}/price', name: 'api_change_product_price', methods: ['PATCH'])]
    public function __invoke(
        string $productId,
        #[MapRequestPayload] ChangeProductPriceRequest $request
    ): JsonResponse {
        $command = ChangeProductPriceCommand::fromRequest($productId, $request);

        $this->commandBus->dispatch($command);

        return $this->responder->productPriceChanged($command->productId);
    }
}
