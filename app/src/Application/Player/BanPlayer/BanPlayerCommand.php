<?php

namespace App\Application\Player\BanPlayer;

use Symfony\Component\Validator\Constraints as Assert;

final class BanPlayerCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'id is required')]
        #[Assert\Uuid(message: 'id must be a valid UUID')]
        public readonly string $id
    ) {}
}
