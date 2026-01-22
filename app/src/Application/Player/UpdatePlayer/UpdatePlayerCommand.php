<?php

namespace App\Application\Player\UpdatePlayer;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdatePlayerCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'id is required')]
        #[Assert\Uuid(message: 'id must be a valid UUID')]
        public readonly string $id,

        public readonly ?string $login = null,
        public readonly ?string $email = null,
        public readonly ?string $phone = null,
        public readonly ?string $name = null,
        public readonly ?string $surname = null,
        public readonly ?string $nickname = null,
        public readonly ?string $currency = null,
        public readonly ?string $country = null,
    ) {}
}
