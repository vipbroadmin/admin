<?php

namespace App\Controller\Api\V1;

use App\Application\Report\ListTransactions\ListTransactionsHandler;
use App\Application\Report\ListTransactions\ListTransactionsQuery;
use App\Infrastructure\Http\Exception\ExternalServiceException;
use App\Infrastructure\Http\Exception\ExternalServiceValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/finances/api/v1/reports')]
final class ReportsController extends AbstractController
{
    #[Route('/transactions', methods: ['GET'])]
    public function listTransactions(
        Request $request,
        ValidatorInterface $validator,
        ListTransactionsHandler $handler
    ): JsonResponse {
        $limit = $request->query->has('limit') ? $request->query->getInt('limit') : null;
        $offset = $request->query->has('offset') ? $request->query->getInt('offset') : null;

        $query = new ListTransactionsQuery(
            playerId: $request->query->get('playerId'),
            walletId: $request->query->get('walletId'),
            currency: $request->query->get('currency'),
            type: $request->query->get('type'),
            reason: $request->query->get('reason'),
            from: $request->query->get('from'),
            to: $request->query->get('to'),
            sortBy: $request->query->get('sortBy'),
            order: $request->query->get('order'),
            limit: $limit,
            offset: $offset,
        );

        $errors = $validator->validate($query);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'details' => $this->violationsToArray($errors),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $handler->handle($query);
            $total = $result['total'] ?? (isset($result['items']) && is_array($result['items']) ? count($result['items']) : count($result));

            if ($offset !== null && $offset > 0 && $offset >= $total) {
                return $this->json([
                    'error' => [
                        'code' => 'not_found',
                        'message' => 'Requested range exceeds total items',
                    ],
                ], Response::HTTP_NOT_FOUND);
            }

            return $this->json($result);
        } catch (ExternalServiceValidationException $e) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @param \Symfony\Component\Validator\ConstraintViolationListInterface $violations
     * @return array<int, array{field: string, message: string}>
     */
    private function violationsToArray($violations): array
    {
        $out = [];
        foreach ($violations as $v) {
            $out[] = [
                'field' => $v->getPropertyPath(),
                'message' => $v->getMessage(),
            ];
        }
        return $out;
    }
}
