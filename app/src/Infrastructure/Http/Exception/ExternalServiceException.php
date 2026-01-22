<?php

namespace App\Infrastructure\Http\Exception;

class ExternalServiceException extends \RuntimeException
{
    public function __construct(
        string $message = '',
        private readonly int $statusCode = 0,
        private readonly string $errorCode = '',
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, 0, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }
}
