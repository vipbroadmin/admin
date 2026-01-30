<?php

namespace App\Controller\Api\V1;

use App\Application\Slotegrator\GetGames\GetGamesHandler;
use App\Application\Slotegrator\GetGames\GetGamesQuery;
use App\Application\Slotegrator\GetProviders\GetProvidersHandler;
use App\Application\Slotegrator\GetProviders\GetProvidersQuery;
use App\Application\Slotegrator\LaunchGame\LaunchGameCommand;
use App\Application\Slotegrator\LaunchGame\LaunchGameHandler;
use App\Infrastructure\Http\Exception\ExternalServiceException;
use App\Infrastructure\Http\Exception\ExternalServiceNotFoundException;
use App\Infrastructure\Http\Exception\ExternalServiceValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/v1/slotegrator')]
final class SlotegratorController extends AbstractController
{
    #[Route('/providers', methods: ['GET'])]
    public function providers(
        Request $request,
        ValidatorInterface $validator,
        GetProvidersHandler $handler
    ): JsonResponse {
        $query = new GetProvidersQuery(
            status: $request->query->get('status'),
            search: $request->query->get('search'),
            limit: $request->query->has('limit') ? $request->query->getInt('limit') : null,
            offset: $request->query->has('offset') ? $request->query->getInt('offset') : null,
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
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/games', methods: ['GET'])]
    public function games(
        Request $request,
        ValidatorInterface $validator,
        GetGamesHandler $handler
    ): JsonResponse {
        $query = new GetGamesQuery(
            providerId: $request->query->has('provider_id') ? $request->query->getInt('provider_id') : null,
            status: $request->query->get('status'),
            search: $request->query->get('search'),
            limit: $request->query->has('limit') ? $request->query->getInt('limit') : null,
            offset: $request->query->has('offset') ? $request->query->getInt('offset') : null,
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
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/launch', methods: ['POST'])]
    public function launch(
        Request $request,
        ValidatorInterface $validator,
        LaunchGameHandler $handler
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

        $cmd = new LaunchGameCommand(
            gameUuid: $payload['game_uuid'] ?? null,
            playerId: $payload['player_id'] ?? null,
            playerName: $payload['player_name'] ?? null,
            currency: $payload['currency'] ?? null,
            sessionId: $payload['session_id'] ?? null,
            device: $payload['device'] ?? null,
            returnUrl: $payload['return_url'] ?? null,
            language: $payload['language'] ?? null,
            email: $payload['email'] ?? null,
            lobbyData: $payload['lobby_data'] ?? null,
            demo: $payload['demo'] ?? null,
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
            $result = $handler->handle($cmd);
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
