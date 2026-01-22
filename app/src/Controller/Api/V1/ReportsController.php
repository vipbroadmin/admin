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

#[Route('/api/v1/finances/reports')]
final class ReportsController extends AbstractController
{
    #[Route('/transactions', methods: ['GET'])]
    public function listTransactions(
        Request $request,
        ValidatorInterface $validator,
        ListTransactionsHandler $handler
    ): JsonResponse {
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
            limit: $request->query->getInt('limit') ?: null,
            offset: $request->query->getInt('offset') ?: null,
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
