<?php

namespace App\Controller\Api\V1;

use App\Infrastructure\Http\Exception\ExternalServiceException;
use App\Infrastructure\Http\WithdrawalsServiceClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/finances/deposit-requests')]
final class DepositRequestController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(Request $request, WithdrawalsServiceClient $client): JsonResponse
    {
        $params = [
            'limit' => $request->query->get('limit') !== null && $request->query->get('limit') !== ''
                ? (int) $request->query->get('limit') : null,
            'offset' => $request->query->get('offset') !== null && $request->query->get('offset') !== ''
                ? (int) $request->query->get('offset') : null,
            'userId' => $request->query->get('userId'),
        ];
        $params = array_filter($params, fn($v) => $v !== null && $v !== '');

        try {
            $result = $client->listDepositRequests($params);
            return $this->json($result);
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
