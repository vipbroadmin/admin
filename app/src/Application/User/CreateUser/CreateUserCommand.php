<?php

namespace App\Application\User\CreateUser;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateUserCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'name is required')]
        #[Assert\Type(type: 'string', message: 'name must be a string')]
        #[Assert\Length(max: 90, maxMessage: 'name must be at most 90 characters')]
        public mixed $name,

        #[Assert\NotBlank(message: 'email is required')]
        #[Assert\Type(type: 'string', message: 'email must be a string')]
        #[Assert\Email(message: 'email must be a valid email address')]
        public mixed $email,
    ) {}
}
