<?php

namespace App\Application\Player\UpdatePassword;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdatePasswordCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'id is required')]
        #[Assert\Uuid(message: 'id must be a valid UUID')]
        public readonly string $id,

        #[Assert\NotBlank(message: 'password is required')]
        #[Assert\Type(type: 'string', message: 'password must be a string')]
        public readonly string $password,
    ) {}
}
