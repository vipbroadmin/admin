<?php

namespace App\Application\User\GetUser;

use App\Domain\User\UserId;
use App\Domain\User\UserRepository;

final class GetUserHandler
{
    public function __construct(private UserRepository $users) {}

    public function handle(GetUserQuery $query): ?array
    {
        $userId = UserId::fromInt((int)$query->id);

        $user = $this->users->getById($userId);
        return $user?->toArray();
    }
}
