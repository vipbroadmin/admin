<?php

namespace App\Application\User\CreateStaffUser;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class CreateStaffUserHandler
{
    public function __construct(
        private UserRepository $users,
        private EntityManagerInterface $entityManager,
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    /**
     * @return array{id: int, username: string, roles: array<int, string>, isBlocked: bool}
     */
    public function handle(CreateStaffUserCommand $command): array
    {
        $existing = $this->users->findOneBy(['username' => $command->username]);
        if ($existing !== null) {
            throw new \InvalidArgumentException('username already exists');
        }

        $user = (new User())
            ->setUsername($command->username)
            ->setRoles($command->roles ?? []);

        $hash = $this->passwordHasher->hashPassword($user, $command->password);
        $user->setPassword($hash);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return [
            'id' => $user->getId(),
            'username' => $user->getUserIdentifier(),
            'roles' => $user->getRoles(),
            'isBlocked' => $user->isBlocked(),
        ];
    }
}
