<?php

namespace App\Infrastructure\Persistence;

use App\Domain\User\User;
use App\Domain\User\UserId;
use App\Domain\User\UserRepository;

final class InMemoryUserRepository implements UserRepository
{
    /** @var array<int, User> */
    private array $storage = [];

    private int $autoIncrement = 1000;

    public function getById(UserId $id): ?User
    {
        return $this->storage[$id->value()] ?? null;
    }

    public function save(User $user): void
    {
        $this->storage[$user->id()->value()] = $user;
    }

    public function nextId(): UserId
    {
        $this->autoIncrement++;
        return UserId::fromInt($this->autoIncrement);
    }
}
