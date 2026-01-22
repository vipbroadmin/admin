<?php

namespace App\Application\Player\UpdatePlayerDocumentStatus;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdatePlayerDocumentStatusCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'id is required')]
        #[Assert\Uuid(message: 'id must be a valid UUID')]
        public readonly string $id,

        #[Assert\NotBlank(message: 'status is required')]
        #[Assert\Type(type: 'string', message: 'status must be a string')]
        public readonly string $status,
    ) {}
}
