<?php

namespace App\Domain\User;

interface UserRepository
{
    public function getById(UserId $id): ?User;

    public function save(User $user): void;

    public function nextId(): UserId;
}
