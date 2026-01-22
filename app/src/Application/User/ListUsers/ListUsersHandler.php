<?php

namespace App\Application\User\ListUsers;

use App\Repository\UserRepository;

final class ListUsersHandler
{
    public function __construct(private UserRepository $users) {}

    /**
     * @return array{items: array<int, array{id: int, username: string, roles: array<int, string>, isBlocked: bool}>, total: int}
     */
    public function handle(ListUsersQuery $query): array
    {
        $items = $this->users->findBy([], ['id' => 'ASC'], $query->limit, $query->offset);

        return [
            'items' => array_map(
                static fn($user) => [
                    'id' => $user->getId(),
                    'username' => $user->getUserIdentifier(),
                    'roles' => $user->getRoles(),
                    'isBlocked' => $user->isBlocked(),
                ],
                $items
            ),
            'total' => $this->users->count([]),
        ];
    }
}
