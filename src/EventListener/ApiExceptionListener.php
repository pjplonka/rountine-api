<?php

namespace App\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Validator\Exception\ValidationFailedException;

class ApiExceptionListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 10],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();
        $request = $event->getRequest();

        if (!str_starts_with($request->getPathInfo(), '/v1') && !str_starts_with($request->getPathInfo(), '/api')) {
            return;
        }

        $response = null;

        if ($exception instanceof UnprocessableEntityHttpException && $exception->getPrevious() instanceof ValidationFailedException) {
            $validationException = $exception->getPrevious();
            $errors = [];
            foreach ($validationException->getViolations() as $violation) {
                $errors[] = [
                    'property' => $violation->getPropertyPath(),
                    'message' => $violation->getMessage(),
                ];
            }

            $response = new JsonResponse([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $errors
            ], 422);
        } elseif ($exception instanceof NotFoundHttpException) {
            $response = new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage() ?: 'Resource not found'
            ], 404);
        } elseif ($exception instanceof HttpExceptionInterface) {
            $response = new JsonResponse([
                'status' => 'error',
                'message' => $exception->getMessage()
            ], $exception->getStatusCode());
        }

        if ($response) {
            $event->setResponse($response);
        }
    }
}
