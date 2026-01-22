<?php

namespace App\Application\User\CreateStaffUser;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateStaffUserCommand
{
    /**
     * @param string[]|null $roles
     */
    public function __construct(
        #[Assert\NotBlank(message: 'username is required')]
        #[Assert\Type(type: 'string', message: 'username must be a string')]
        public readonly string $username,

        #[Assert\NotBlank(message: 'password is required')]
        #[Assert\Type(type: 'string', message: 'password must be a string')]
        public readonly string $password,

        #[Assert\Type(type: 'array', message: 'roles must be an array')]
        public readonly ?array $roles = null,
    ) {}
}
