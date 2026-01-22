<?php

namespace App\Application\User\CreateUser;

use App\Domain\User\Email;
use App\Domain\User\User;
use App\Domain\User\UserName;
use App\Domain\User\UserRepository;

final class CreateUserHandler
{
    public function __construct(private UserRepository $users) {}

    public function handle(CreateUserCommand $cmd): array
    {
        $id = $this->users->nextId();

        $user = User::create(
            $id,
            UserName::fromString((string)$cmd->name),
            Email::fromString((string)$cmd->email),
        );

        $this->users->save($user);

        return $user->toArray();
    }
}
