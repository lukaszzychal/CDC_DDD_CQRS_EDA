<?php

declare(strict_types=1);

namespace App\Backend\Infrastructure\EventSubscriber;

use App\Backend\Domain\Exception\ProductAlreadyExistsException;
use App\Backend\Domain\Exception\ProductNotFoundException;
use DomainException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Messenger\Exception\HandlerFailedException;

final readonly class ApiExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => 'onKernelException',
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        if ($exception instanceof HandlerFailedException && $exception->getPrevious()) {
            $exception = $exception->getPrevious();
        }

        if ($exception instanceof ProductNotFoundException) {
            $event->setResponse(new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], Response::HTTP_NOT_FOUND));
            return;
        }

        if ($exception instanceof ProductAlreadyExistsException) {
            $event->setResponse(new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], Response::HTTP_CONFLICT));
            return;
        }

        if ($exception instanceof DomainException) {
            $event->setResponse(new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], Response::HTTP_BAD_REQUEST));
            return;
        }
    }
}
