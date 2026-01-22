<?php

namespace App\Controller\Api\V1;

use App\Application\Withdrawal\CreateWithdrawal\CreateWithdrawalCommand;
use App\Application\Withdrawal\GetWithdrawal\GetWithdrawalQuery;
use App\Application\Withdrawal\ManagerAction\ManagerActionCommand;
use App\Application\Withdrawal\RejectWithdrawal\RejectWithdrawalCommand;
use App\Infrastructure\Http\Exception\ExternalServiceException;
use App\Infrastructure\Http\Exception\ExternalServiceNotFoundException;
use App\Infrastructure\Http\Exception\ExternalServiceValidationException;
use App\Infrastructure\Http\WithdrawalsServiceClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/finances/withdrawal-requests')]
final class WithdrawalController extends AbstractController
{
    #[Route('/{id}', methods: ['GET'])]
    public function getById(
        string $id,
        ValidatorInterface $validator,
        WithdrawalsServiceClient $client
    ): JsonResponse {
        $query = new GetWithdrawalQuery(id: $id);

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
            $result = $client->getById($query->id);
            return $this->json($result);
        } catch (ExternalServiceNotFoundException $e) {
            return $this->json([
                'error' => [
                    'code' => 'not_found',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_NOT_FOUND);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        WithdrawalsServiceClient $client
    ): JsonResponse {
        $payload = json_decode($request->getContent() ?: '', true);
        if (!is_array($payload)) {
            return $this->json([
                'error' => [
                    'code' => 'invalid_json',
                    'message' => 'Request body must be valid JSON.',
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $cmd = new CreateWithdrawalCommand(
            userId: (string)($payload['userId'] ?? ''),
            currency: (string)($payload['currency'] ?? ''),
            amount: (string)($payload['amount'] ?? ''),
            paymentSystemId: (string)($payload['paymentSystemId'] ?? ''),
        );

        $errors = $validator->validate($cmd);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'details' => $this->violationsToArray($errors),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        try {
            $result = $client->create([
                'userId' => $cmd->userId,
                'currency' => $cmd->currency,
                'amount' => $cmd->amount,
                'paymentSystemId' => $cmd->paymentSystemId,
            ]);
            return $this->json($result, Response::HTTP_CREATED);
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

    #[Route('/{id}/take', methods: ['POST'])]
    public function take(
        string $id,
        Request $request,
        ValidatorInterface $validator,
        WithdrawalsServiceClient $client
    ): JsonResponse {
        $cmd = $this->buildManagerCommand($id, $request);
        $errors = $validator->validate($cmd);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'details' => $this->violationsToArray($errors),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $client->take($cmd->id, $cmd->managerId, $cmd->managerRole);
            return $this->json($result);
        } catch (ExternalServiceValidationException $e) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ExternalServiceNotFoundException $e) {
            return $this->json([
                'error' => [
                    'code' => 'not_found',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_NOT_FOUND);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/request-verification', methods: ['POST'])]
    public function requestVerification(
        string $id,
        Request $request,
        ValidatorInterface $validator,
        WithdrawalsServiceClient $client
    ): JsonResponse {
        $cmd = $this->buildManagerCommand($id, $request);
        $errors = $validator->validate($cmd);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'details' => $this->violationsToArray($errors),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $client->requestVerification($cmd->id, $cmd->managerId, $cmd->managerRole);
            return $this->json($result);
        } catch (ExternalServiceValidationException $e) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ExternalServiceNotFoundException $e) {
            return $this->json([
                'error' => [
                    'code' => 'not_found',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_NOT_FOUND);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/approve', methods: ['POST'])]
    public function approve(
        string $id,
        Request $request,
        ValidatorInterface $validator,
        WithdrawalsServiceClient $client
    ): JsonResponse {
        $cmd = $this->buildManagerCommand($id, $request);
        $errors = $validator->validate($cmd);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'details' => $this->violationsToArray($errors),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $client->approve($cmd->id, $cmd->managerId, $cmd->managerRole);
            return $this->json($result);
        } catch (ExternalServiceValidationException $e) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ExternalServiceNotFoundException $e) {
            return $this->json([
                'error' => [
                    'code' => 'not_found',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_NOT_FOUND);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/reject', methods: ['POST'])]
    public function reject(
        string $id,
        Request $request,
        ValidatorInterface $validator,
        WithdrawalsServiceClient $client
    ): JsonResponse {
        $payload = json_decode($request->getContent() ?: '', true);
        if (!is_array($payload)) {
            return $this->json([
                'error' => [
                    'code' => 'invalid_json',
                    'message' => 'Request body must be valid JSON.',
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $cmd = new RejectWithdrawalCommand(
            id: $id,
            managerId: (string)$request->headers->get('X-Manager-Id', ''),
            managerRole: (string)$request->headers->get('X-Manager-Role', ''),
            reason: (string)($payload['reason'] ?? ''),
        );

        $errors = $validator->validate($cmd);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'details' => $this->violationsToArray($errors),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $client->reject($cmd->id, $cmd->managerId, $cmd->managerRole, $cmd->reason);
            return $this->json($result);
        } catch (ExternalServiceValidationException $e) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ExternalServiceNotFoundException $e) {
            return $this->json([
                'error' => [
                    'code' => 'not_found',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_NOT_FOUND);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/retry', methods: ['POST'])]
    public function retry(
        string $id,
        Request $request,
        ValidatorInterface $validator,
        WithdrawalsServiceClient $client
    ): JsonResponse {
        $cmd = $this->buildManagerCommand($id, $request);
        $errors = $validator->validate($cmd);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'details' => $this->violationsToArray($errors),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $client->retry($cmd->id, $cmd->managerId, $cmd->managerRole);
            return $this->json($result);
        } catch (ExternalServiceValidationException $e) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ExternalServiceNotFoundException $e) {
            return $this->json([
                'error' => [
                    'code' => 'not_found',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_NOT_FOUND);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/return', methods: ['POST'])]
    public function returnRequest(
        string $id,
        Request $request,
        ValidatorInterface $validator,
        WithdrawalsServiceClient $client
    ): JsonResponse {
        $cmd = $this->buildManagerCommand($id, $request);
        $errors = $validator->validate($cmd);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'details' => $this->violationsToArray($errors),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $result = $client->returnRequest($cmd->id, $cmd->managerId, $cmd->managerRole);
            return $this->json($result);
        } catch (ExternalServiceValidationException $e) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        } catch (ExternalServiceNotFoundException $e) {
            return $this->json([
                'error' => [
                    'code' => 'not_found',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_NOT_FOUND);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    private function buildManagerCommand(string $id, Request $request): ManagerActionCommand
    {
        return new ManagerActionCommand(
            id: $id,
            managerId: (string)$request->headers->get('X-Manager-Id', ''),
            managerRole: (string)$request->headers->get('X-Manager-Role', ''),
        );
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
