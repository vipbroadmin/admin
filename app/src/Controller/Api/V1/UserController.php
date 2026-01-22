<?php

namespace App\Controller\Api\V1;

use App\Application\User\CreateUser\CreateUserCommand;
use App\Application\User\CreateUser\CreateUserHandler;
use App\Application\User\GetUser\GetUserHandler;
use App\Application\User\GetUser\GetUserQuery;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/user/v1')]
final class UserController extends AbstractController
{
    #[Route('', methods: ['GET'])]
    public function get(
        Request $request,
        ValidatorInterface $validator,
        GetUserHandler $handler
    ): JsonResponse {
        $query = new GetUserQuery($request->query->get('id'));

        $errors = $validator->validate($query);
        if (count($errors) > 0) {
            return $this->json([
                'error' => [
                    'code' => 'validation_failed',
                    'details' => $this->violationsToArray($errors),
                ],
            ], Response::HTTP_BAD_REQUEST);
        }

        $user = $handler->handle($query);

        if ($user === null) {
            return $this->json([
                'error' => [
                    'code' => 'not_found',
                    'message' => 'User not found',
                ],
            ], Response::HTTP_NOT_FOUND);
        }

        return $this->json(['data' => $user]);
    }

    #[Route('', methods: ['POST'])]
    public function post(
        Request $request,
        ValidatorInterface $validator,
        CreateUserHandler $handler
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

        $cmd = new CreateUserCommand(
            $payload['name'] ?? null,
            $payload['email'] ?? null,
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
            // доменная валидация (value objects)
            return $this->json([
                'error' => [
                    'code' => 'domain_validation_failed',
                    'message' => $e->getMessage(),
                ],
            ], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        return $this->json(['data' => $user], Response::HTTP_CREATED);
    }

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
