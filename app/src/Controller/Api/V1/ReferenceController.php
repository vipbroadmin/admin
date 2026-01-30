<?php

namespace App\Controller\Api\V1;

use App\Infrastructure\Http\Exception\ExternalServiceException;
use App\Infrastructure\Http\ReferenceServiceClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/reference/api/v1')]
final class ReferenceController extends AbstractController
{
    #[Route('/countries', methods: ['GET'])]
    public function countries(ReferenceServiceClient $client): JsonResponse
    {
        try {
            $countries = $client->getCountries();
            return $this->json($countries);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/currencies', methods: ['GET'])]
    public function currencies(ReferenceServiceClient $client): JsonResponse
    {
        try {
            $currencies = $client->getCurrencies();
            return $this->json($currencies);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
