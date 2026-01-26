<?php

namespace App\Controller\Api\V1;

use App\Application\Wallet\Cashout\WalletCashoutCommand;
use App\Application\Wallet\ConfirmWithdrawal\ConfirmWithdrawalCommand;
use App\Application\Wallet\ConfirmWithdrawal\ConfirmWithdrawalHandler;
use App\Application\Wallet\CreateWallet\CreateWalletCommand;
use App\Application\Wallet\Deposit\WalletDepositCommand;
use App\Application\Wallet\GetWallets\GetWalletsQuery;
use App\Application\Wallet\Unlock\WalletUnlockCommand;
use App\Infrastructure\Http\Exception\ExternalServiceException;
use App\Infrastructure\Http\Exception\ExternalServiceNotFoundException;
use App\Infrastructure\Http\Exception\ExternalServiceValidationException;
use App\Infrastructure\Http\WalletsServiceClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/finances/wallets')]
final class WalletController extends AbstractController
{
    #[Route('/by-player-id/{playerId}', methods: ['GET'])]
    public function getByPlayerId(
        string $playerId,
        Request $request,
        ValidatorInterface $validator,
        WalletsServiceClient $client
    ): JsonResponse {
        $query = new GetWalletsQuery(playerId: $playerId);
        $offset = $request->query->has('offset') ? $request->query->getInt('offset') : null;

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
            $wallets = $client->getWalletsByPlayerId($query->playerId);
            $total = $wallets['total'] ?? (isset($wallets['wallets']) && is_array($wallets['wallets']) ? count($wallets['wallets']) : count($wallets));

            if ($offset !== null && $offset > 0 && $offset >= $total) {
                return $this->json([
                    'error' => [
                        'code' => 'not_found',
                        'message' => 'Requested range exceeds total items',
                    ],
                ], Response::HTTP_NOT_FOUND);
            }

            return $this->json($wallets);
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
        WalletsServiceClient $client
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

        $cmd = new CreateWalletCommand(
            playerId: (string)($payload['playerId'] ?? ''),
            currency: (string)($payload['currency'] ?? ''),
            type: isset($payload['type']) ? (string)$payload['type'] : null,
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
            $wallet = $client->createWallet([
                'playerId' => $cmd->playerId,
                'currency' => $cmd->currency,
                'type' => $cmd->type,
            ]);
            return $this->json($wallet, Response::HTTP_CREATED);
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

    #[Route('/deposit', methods: ['POST'])]
    public function deposit(
        Request $request,
        ValidatorInterface $validator,
        WalletsServiceClient $client
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

        $cmd = new WalletDepositCommand(
            playerId: (string)($payload['playerId'] ?? ''),
            walletId: (string)($payload['walletId'] ?? ''),
            amount: (string)($payload['amount'] ?? ''),
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
            $wallet = $client->deposit([
                'playerId' => $cmd->playerId,
                'walletId' => $cmd->walletId,
                'amount' => $cmd->amount,
            ]);
            return $this->json($wallet);
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

    #[Route('/cashout', methods: ['POST'])]
    public function cashout(
        Request $request,
        ValidatorInterface $validator,
        WalletsServiceClient $client
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

        $cmd = new WalletCashoutCommand(
            playerId: (string)($payload['playerId'] ?? ''),
            walletId: (string)($payload['walletId'] ?? ''),
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
            $result = $client->cashout([
                'playerId' => $cmd->playerId,
                'walletId' => $cmd->walletId,
                'amount' => $cmd->amount,
                'paymentSystemId' => $cmd->paymentSystemId,
            ]);
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

    #[Route('/unlock', methods: ['POST'])]
    public function unlock(
        Request $request,
        ValidatorInterface $validator,
        WalletsServiceClient $client
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

        $cmd = new WalletUnlockCommand(
            playerId: (string)($payload['playerId'] ?? ''),
            walletId: (string)($payload['walletId'] ?? ''),
            amount: (string)($payload['amount'] ?? ''),
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
            $wallet = $client->unlock([
                'playerId' => $cmd->playerId,
                'walletId' => $cmd->walletId,
                'amount' => $cmd->amount,
            ]);
            return $this->json($wallet);
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

    #[Route('/confirm-withdrawal', methods: ['POST'])]
    public function confirmWithdrawal(
        Request $request,
        ValidatorInterface $validator,
        ConfirmWithdrawalHandler $handler
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

        $cmd = new ConfirmWithdrawalCommand(
            withdrawalRequestId: (string)($payload['withdrawalRequestId'] ?? ''),
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
            $wallet = $handler->handle($cmd);
            return $this->json($wallet);
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
