<?php

namespace App\Application\User\BlockUser;

use Symfony\Component\Validator\Constraints as Assert;

final class BlockUserCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'id is required')]
        #[Assert\Type(type: 'integer', message: 'id must be an integer')]
        public readonly int $id,

        public readonly bool $blocked = true,
    ) {}
}
