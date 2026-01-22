<?php

namespace App\Application\User\BlockUser;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;

final class BlockUserHandler
{
    public function __construct(
        private UserRepository $users,
        private EntityManagerInterface $entityManager
    ) {}

    public function handle(BlockUserCommand $command): bool
    {
        $user = $this->users->find($command->id);
        if ($user === null) {
            return false;
        }

        $user->setBlocked($command->blocked);
        $this->entityManager->flush();

        return true;
    }
}
