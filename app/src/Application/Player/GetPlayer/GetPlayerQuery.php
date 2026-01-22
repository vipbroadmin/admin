<?php

namespace App\Application\Player\GetPlayer;

use Symfony\Component\Validator\Constraints as Assert;

final class GetPlayerQuery
{
    public function __construct(
        #[Assert\NotBlank(message: 'id is required')]
        #[Assert\Uuid(message: 'id must be a valid UUID')]
        public readonly string $id
    ) {}
}
