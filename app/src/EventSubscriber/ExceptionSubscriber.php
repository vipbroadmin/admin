<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * In production (when kernel.debug is false), replaces exception responses
 * with safe JSON payloads: no stack traces, no internal messages.
 */
final class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly bool $kernelDebug,
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 0],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        if ($this->kernelDebug) {
            return;
        }

        $throwable = $event->getThrowable();
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;
        $message = 'An error occurred.';

        if ($throwable instanceof NotFoundHttpException) {
            $statusCode = Response::HTTP_NOT_FOUND;
            $message = 'Not found.';
        } elseif ($throwable instanceof MethodNotAllowedHttpException) {
            $statusCode = Response::HTTP_METHOD_NOT_ALLOWED;
            $message = 'Method not allowed.';
        } elseif ($throwable instanceof HttpException) {
            $statusCode = $throwable->getStatusCode();
            $message = $this->safeMessageForStatusCode($statusCode);
        }

        $event->setResponse(new JsonResponse([
            'error' => [
                'message' => $message,
            ],
        ], $statusCode, [
            'Content-Type' => 'application/json',
        ]));
    }

    private function safeMessageForStatusCode(int $code): string
    {
        return match ($code) {
            Response::HTTP_BAD_REQUEST => 'Bad request.',
            Response::HTTP_UNAUTHORIZED => 'Unauthorized.',
            Response::HTTP_FORBIDDEN => 'Forbidden.',
            Response::HTTP_NOT_FOUND => 'Not found.',
            Response::HTTP_METHOD_NOT_ALLOWED => 'Method not allowed.',
            Response::HTTP_UNPROCESSABLE_ENTITY => 'Unprocessable entity.',
            Response::HTTP_TOO_MANY_REQUESTS => 'Too many requests.',
            default => 'An error occurred.',
        };
    }
}
