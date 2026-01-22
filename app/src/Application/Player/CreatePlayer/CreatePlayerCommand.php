<?php

namespace App\Application\Player\CreatePlayer;

use Symfony\Component\Validator\Constraints as Assert;

final class CreatePlayerCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'login is required')]
        #[Assert\Type(type: 'string', message: 'login must be a string')]
        public readonly string $login,

        #[Assert\NotBlank(message: 'password is required')]
        #[Assert\Type(type: 'string', message: 'password must be a string')]
        public readonly string $password,

        #[Assert\NotBlank(message: 'country is required')]
        #[Assert\Type(type: 'string', message: 'country must be a string')]
        public readonly string $country,

        #[Assert\NotBlank(message: 'currency is required')]
        #[Assert\Type(type: 'string', message: 'currency must be a string')]
        public readonly string $currency,

        public readonly ?string $promoCode = null,
    ) {}
}
