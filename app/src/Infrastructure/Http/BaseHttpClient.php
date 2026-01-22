<?php

namespace App\Infrastructure\Http;

use App\Infrastructure\Http\Exception\ExternalServiceException;
use App\Infrastructure\Http\Exception\ExternalServiceNotFoundException;
use App\Infrastructure\Http\Exception\ExternalServiceValidationException;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class BaseHttpClient
{
    public function __construct(
        protected HttpClientInterface $httpClient,
        protected string $baseUrl,
        protected int $timeout = 10
    ) {}

    /**
     * @param array<string, mixed> $options
     */
    protected function request(string $method, string $endpoint, array $options = []): ResponseInterface
    {
        $url = rtrim($this->baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $defaultOptions = [
            'timeout' => $this->timeout,
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
        ];

        $options = array_merge_recursive($defaultOptions, $options);

        return $this->httpClient->request($method, $url, $options);
    }

    /**
     * @return array<string, mixed>
     */
    protected function decodeResponse(ResponseInterface $response): array
    {
        $statusCode = $response->getStatusCode();
        $content = $response->getContent(false);
        $normalizedContent = trim($content);

        if ($statusCode >= 200 && $statusCode < 300) {
            if ($normalizedContent === '' || $normalizedContent === 'true' || $normalizedContent === 'false') {
                return ['success' => $normalizedContent === 'true' || $normalizedContent === ''];
            }

            $data = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new ExternalServiceException(
                    'Invalid JSON response from external service',
                    $statusCode
                );
            }

            if (!is_array($data)) {
                return ['success' => (bool) $data];
            }

            return $data;
        }

        $this->handleError($statusCode, $content);
    }

    protected function handleError(int $statusCode, string $content): never
    {
        $errorData = [];
        if ($content !== '') {
            $decoded = json_decode($content, true);
            if (is_array($decoded)) {
                $errorData = $decoded;
            }
        }

        $errorCode = $errorData['error'] ?? 'unknown';
        if (is_array($errorCode)) {
            $errorCode = (string)($errorCode['code'] ?? $errorCode['error'] ?? 'unknown');
        }
        $message = $errorData['message'] ?? "External service error: HTTP {$statusCode}";

        match ($statusCode) {
            400, 422 => throw new ExternalServiceValidationException(
                $message,
                $statusCode,
                $errorCode
            ),
            404 => throw new ExternalServiceNotFoundException(
                $message,
                $statusCode,
                $errorCode
            ),
            default => throw new ExternalServiceException(
                $message,
                $statusCode,
                $errorCode
            ),
        };
    }
}
