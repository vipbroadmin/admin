<?php

namespace App\Application\Player\UpdatePlayerLevel;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdatePlayerLevelCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'id is required')]
        #[Assert\Uuid(message: 'id must be a valid UUID')]
        public readonly string $id,

        #[Assert\NotNull(message: 'level is required')]
        #[Assert\Type(type: 'integer', message: 'level must be an integer')]
        public readonly int $level,
    ) {}
}
