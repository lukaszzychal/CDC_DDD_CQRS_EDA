<?php

declare(strict_types=1);

namespace App\Backend\UI\HTTP\Action;

use App\Backend\Application\Command\CreateCategoryCommand;
use App\Backend\UI\HTTP\Request\CreateCategoryRequest;
use App\Backend\UI\HTTP\Responder\CreateCategoryResponder;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

#[AsController]
final readonly class CreateCategoryAction
{
    public function __construct(
        private MessageBusInterface $commandBus,
        private CreateCategoryResponder $responder,
    ) {}

    #[Route('api/category', name: 'api_create_category', methods: ['POST'])]
    public function __invoke(
        #[MapRequestPayload] CreateCategoryRequest $request
    ): JsonResponse {
        $command = CreateCategoryCommand::fromRequest($request);

        $this->commandBus->dispatch($command);

        return $this->responder->created($command->id);
    }
}
