<?php

namespace App\Controller\Api\V1;

use App\Application\Player\BanPlayer\BanPlayerCommand;
use App\Application\Player\BanPlayer\BanPlayerHandler;
use App\Application\Player\CreatePlayer\CreatePlayerCommand;
use App\Application\Player\CreatePlayer\CreatePlayerHandler;
use App\Application\Player\GetPlayer\GetPlayerHandler;
use App\Application\Player\GetPlayer\GetPlayerQuery;
use App\Application\Player\GetPlayerDocuments\GetPlayerDocumentsHandler;
use App\Application\Player\GetPlayerDocuments\GetPlayerDocumentsQuery;
use App\Application\Player\GetPlayerRequisites\GetPlayerRequisitesHandler;
use App\Application\Player\GetPlayerRequisites\GetPlayerRequisitesQuery;
use App\Application\Player\GetPlayersInfo\GetPlayersInfoHandler;
use App\Application\Player\GetPlayersInfo\GetPlayersInfoQuery;
use App\Application\Player\KickPlayers\KickPlayersCommand;
use App\Application\Player\KickPlayers\KickPlayersHandler;
use App\Application\Player\ListPlayers\ListPlayersHandler;
use App\Application\Player\ListPlayers\ListPlayersQuery;
use App\Application\Player\UnbanPlayer\UnbanPlayerCommand;
use App\Application\Player\UnbanPlayer\UnbanPlayerHandler;
use App\Application\Player\UpdatePlayerDocumentStatus\UpdatePlayerDocumentStatusCommand;
use App\Application\Player\UpdatePlayerDocumentStatus\UpdatePlayerDocumentStatusHandler;
use App\Application\Player\UpdatePassword\UpdatePasswordCommand;
use App\Application\Player\UpdatePassword\UpdatePasswordHandler;
use App\Application\Player\UpdatePlayer\UpdatePlayerCommand;
use App\Application\Player\UpdatePlayer\UpdatePlayerHandler;
use App\Application\Player\UpdatePlayerLevel\UpdatePlayerLevelCommand;
use App\Application\Player\UpdatePlayerLevel\UpdatePlayerLevelHandler;
use App\Application\Player\UpdatePlayerRequisites\UpdatePlayerRequisitesCommand;
use App\Application\Player\UpdatePlayerRequisites\UpdatePlayerRequisitesHandler;
use App\Infrastructure\Http\Exception\ExternalServiceException;
use App\Infrastructure\Http\Exception\ExternalServiceNotFoundException;
use App\Infrastructure\Http\Exception\ExternalServiceValidationException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/players/api/v1')]
final class PlayerController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(
        Request $request,
        ValidatorInterface $validator,
        ListPlayersHandler $handler
    ): JsonResponse {
        $query = new ListPlayersQuery(
            offset: $request->query->getInt('offset'),
            limit: $request->query->getInt('limit'),
            search: $request->query->get('search'),
            country: $request->query->get('country'),
            currency: $request->query->get('currency'),
            sortBy: $request->query->get('sortBy'),
            order: $request->query->get('order'),
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
            $payload = $result->toArray();
            $total = $payload['total'] ?? count($payload['items'] ?? []);

            if ($query->offset > 0 && $query->offset >= $total) {
                return $this->json([
                    'error' => [
                        'code' => 'not_found',
                        'message' => 'Requested range exceeds total items',
                    ],
                ], Response::HTTP_NOT_FOUND);
            }

            return $this->json($payload);
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
        CreatePlayerHandler $handler
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

        $cmd = new CreatePlayerCommand(
            login: $payload['login'] ?? '',
            password: $payload['password'] ?? '',
            country: $payload['country'] ?? '',
            currency: $payload['currency'] ?? '',
            promoCode: $payload['promoCode'] ?? null,
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
            $handler->handle($cmd);
            return $this->json(['success' => true], Response::HTTP_CREATED);
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

    #[Route('/{id}', methods: ['GET'])]
    public function get(
        string $id,
        ValidatorInterface $validator,
        GetPlayerHandler $handler
    ): JsonResponse {
        $query = new GetPlayerQuery(id: $id);

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
            $player = $handler->handle($query);
            if ($player === null) {
                return $this->json([
                    'error' => [
                        'code' => 'not_found',
                        'message' => 'Player not found',
                    ],
                ], Response::HTTP_NOT_FOUND);
            }

            return $this->json(['data' => $player->toArray()]);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}/documents', methods: ['GET'])]
    public function documents(
        string $id,
        ValidatorInterface $validator,
        GetPlayerDocumentsHandler $handler
    ): JsonResponse {
        $query = new GetPlayerDocumentsQuery(id: $id);

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
            $documents = $handler->handle($query);
            return $this->json($documents);
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

    #[Route('/document/{id}', methods: ['PATCH'])]
    public function updateDocumentStatus(
        string $id,
        Request $request,
        ValidatorInterface $validator,
        UpdatePlayerDocumentStatusHandler $handler
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

        $cmd = new UpdatePlayerDocumentStatusCommand(
            id: $id,
            status: (string)($payload['status'] ?? ''),
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
            $handler->handle($cmd);
            return $this->json(['success' => true]);
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

    #[Route('/info', methods: ['POST'])]
    public function getInfo(
        Request $request,
        ValidatorInterface $validator,
        GetPlayersInfoHandler $handler
    ): JsonResponse {
        $payload = json_decode($request->getContent() ?: '', true);

        if (!is_array($payload) || !isset($payload['ids']) || !is_array($payload['ids'])) {
            return $this->json([
                'error' => [
                    'code' => 'invalid_json',
                    'message' => 'Request body must contain "ids" array.',
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $query = new GetPlayersInfoQuery(ids: $payload['ids']);

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
            $players = $handler->handle($query);
            return $this->json([
                'data' => array_map(fn($p) => $p->toArray(), $players),
            ]);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/kick', methods: ['POST'])]
    public function kick(
        Request $request,
        ValidatorInterface $validator,
        KickPlayersHandler $handler
    ): JsonResponse {
        $payload = json_decode($request->getContent() ?: '', true);

        if (!is_array($payload)) {
            return $this->json([
                'error' => [
                    'code' => 'invalid_json',
                    'message' => 'Request body must be valid JSON array.',
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $cmd = new KickPlayersCommand(ids: $payload);

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
            $handler->handle($cmd);
            return $this->json(['success' => true]);
        } catch (ExternalServiceException $e) {
            return $this->json([
                'error' => [
                    'code' => 'external_service_error',
                    'message' => $e->getMessage(),
                ],
            ], $e->getStatusCode() ?: Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/{id}', methods: ['PUT'])]
    public function update(
        string $id,
        Request $request,
        ValidatorInterface $validator,
        UpdatePlayerHandler $handler
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

        $cmd = new UpdatePlayerCommand(
            id: $id,
            login: $payload['login'] ?? null,
            email: $payload['email'] ?? null,
            phone: $payload['phone'] ?? null,
            name: $payload['name'] ?? null,
            surname: $payload['surname'] ?? null,
            nickname: $payload['nickname'] ?? null,
            currency: $payload['currency'] ?? null,
            country: $payload['country'] ?? null,
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
            $handler->handle($cmd);
            return $this->json(['success' => true]);
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

    #[Route('/{id}/level', methods: ['PUT'])]
    public function updateLevel(
        string $id,
        Request $request,
        ValidatorInterface $validator,
        UpdatePlayerLevelHandler $handler
    ): JsonResponse {
        $payload = json_decode($request->getContent() ?: '', true);
        if (!is_array($payload) || !array_key_exists('level', $payload)) {
            return $this->json([
                'error' => [
                    'code' => 'invalid_json',
                    'message' => 'Request body must contain "level" field.',
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $cmd = new UpdatePlayerLevelCommand(
            id: $id,
            level: (int)$payload['level'],
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
            $handler->handle($cmd);
            return $this->json(['success' => true]);
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

    #[Route('/{id}/password', methods: ['PUT'])]
    public function updatePassword(
        string $id,
        Request $request,
        ValidatorInterface $validator,
        UpdatePasswordHandler $handler
    ): JsonResponse {
        $payload = json_decode($request->getContent() ?: '', true);

        if (!is_array($payload) || !isset($payload['password'])) {
            return $this->json([
                'error' => [
                    'code' => 'invalid_json',
                    'message' => 'Request body must contain "password" field.',
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $cmd = new UpdatePasswordCommand(
            id: $id,
            password: $payload['password'],
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
            $handler->handle($cmd);
            return $this->json(['success' => true]);
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

    #[Route('/{id}/requisites', methods: ['GET'])]
    public function getRequisites(
        string $id,
        ValidatorInterface $validator,
        GetPlayerRequisitesHandler $handler
    ): JsonResponse {
        $query = new GetPlayerRequisitesQuery(id: $id);

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

    #[Route('/{id}/requisites', methods: ['POST'])]
    public function updateRequisites(
        string $id,
        Request $request,
        ValidatorInterface $validator,
        UpdatePlayerRequisitesHandler $handler
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

        $cmd = new UpdatePlayerRequisitesCommand(
            id: $id,
            paymentMethodId: isset($payload['paymentMethodId']) ? (string)$payload['paymentMethodId'] : null,
            formData: isset($payload['formData']) && is_array($payload['formData']) ? $payload['formData'] : null,
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
            $handler->handle($cmd);
            return $this->json(['success' => true]);
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

    #[Route('/{id}/ban', methods: ['PUT'])]
    public function ban(
        string $id,
        ValidatorInterface $validator,
        BanPlayerHandler $handler
    ): JsonResponse {
        $cmd = new BanPlayerCommand(id: $id);

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
            $handler->handle($cmd);
            return $this->json(['success' => true]);
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

    #[Route('/{id}/unban', methods: ['PUT'])]
    public function unban(
        string $id,
        ValidatorInterface $validator,
        UnbanPlayerHandler $handler
    ): JsonResponse {
        $cmd = new UnbanPlayerCommand(id: $id);

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
            $handler->handle($cmd);
            return $this->json(['success' => true]);
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
