<?php

namespace App\Controller\Api\V1;

use App\Application\User\BlockUser\BlockUserCommand;
use App\Application\User\BlockUser\BlockUserHandler;
use App\Application\User\CreateStaffUser\CreateStaffUserCommand;
use App\Application\User\CreateStaffUser\CreateStaffUserHandler;
use App\Application\User\ListUsers\ListUsersHandler;
use App\Application\User\ListUsers\ListUsersQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/users/staffs')]
final class AuthController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function list(
        Request $request,
        ValidatorInterface $validator,
        ListUsersHandler $handler
    ): JsonResponse {
        //$offset = $request->query->has('offset') ? $request->query->getInt('offset') : null;
        //$limit = $request->query->has('limit') ? $request->query->getInt('limit') : null;
        $offset = $request->query->has('offset') ? max(0, $request->query->getInt('offset')) : 0;
        $limit = $request->query->has('limit') ? max(0, $request->query->getInt('limit')) : null;


        $query = new ListUsersQuery(offset: $offset, limit: $limit);

        $errors = $validator->validate($query);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'details' => $this->violationsToArray($errors),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $result = $handler->handle($query);
        $total  = $result['total'];

        $exceedsTotal = false;

        if ($limit !== null) {
            if ($offset >= $total || ($offset + $limit) > $total) {
                return $this->json([
                    'error' => [
                        'code' => 'not_found',
                        'message' => 'Requested range exceeds total items',
                    ],
                ], Response::HTTP_NOT_FOUND);
            }
        }
        return $this->json($result);
    }

    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        ValidatorInterface $validator,
        CreateStaffUserHandler $handler
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

        $cmd = new CreateStaffUserCommand(
            username: (string)($payload['username'] ?? ''),
            password: (string)($payload['password'] ?? ''),
            roles: isset($payload['roles']) && is_array($payload['roles']) ? $payload['roles'] : null,
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
            $user = $handler->handle($cmd);
        } catch (\InvalidArgumentException $e) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(['data' => $user], Response::HTTP_CREATED);
    }

    #[Route('/me', methods: ['GET'])]
    public function me(): JsonResponse
    {
        $user = $this->getUser();
        if ($user === null) {
            return $this->json([
                'error' => [
                    'code' => 'unauthorized',
                    'message' => 'User is not authenticated.',
                ],
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        return $this->json([
            'data' => [
                'username' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
            ],
        ]);
    }

    #[Route('/logout', methods: ['DELETE'])]
    public function logout(): JsonResponse
    {
        return $this->json(true);
    }

    #[Route('/{id}/ban', methods: ['PUT'])]
    public function ban(
        int $id,
        ValidatorInterface $validator,
        BlockUserHandler $handler
    ): JsonResponse {
        $cmd = new BlockUserCommand(id: $id, blocked: true);

        $errors = $validator->validate($cmd);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'details' => $this->violationsToArray($errors),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $ok = $handler->handle($cmd);
        if (!$ok) {
            return $this->json([
                'error' => [
                    'code' => 'not_found',
                    'message' => 'User not found',
                ],
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['success' => true]);
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
