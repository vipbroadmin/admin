<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

#[Route('/provider/callback', methods: ['POST'])]
final class ProviderCallbackController
{
    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly string $slotegratorBaseUrl,
        private readonly int $timeout = 5,
    ) {}

    public function __invoke(Request $request): Response
    {
        $targetUrl = rtrim($this->slotegratorBaseUrl, '/') . '/slotegrator/callback';

        $headersToForward = [
            'X-Merchant-Id',
            'X-Timestamp',
            'X-Nonce',
            'X-Sign',
        ];

        $headers = [
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
        ];
        foreach ($headersToForward as $header) {
            $value = $request->headers->get($header);
            if ($value !== null && $value !== '') {
                $headers[$header] = $value;
            }
        }

        try {
            $upstream = $this->httpClient->request('POST', $targetUrl, [
                'headers' => $headers,
                'body' => $request->getContent(),
                'timeout' => $this->timeout,
            ]);

            $status = $upstream->getStatusCode();
            $content = $upstream->getContent(false);

            // Slotegrator callback expects HTTP 200 even on errors.
            if ($status < 200 || $status >= 300) {
                return new JsonResponse([
                    'error_code' => 'INTERNAL_ERROR',
                    'error_description' => 'callback proxy error',
                ], Response::HTTP_OK);
            }

            return new Response($content, Response::HTTP_OK, [
                'Content-Type' => 'application/json',
            ]);
        } catch (TransportExceptionInterface|\Throwable $e) {
            return new JsonResponse([
                'error_code' => 'INTERNAL_ERROR',
                'error_description' => 'callback proxy error',
            ], Response::HTTP_OK);
        }
    }
}

