<?php

namespace App\Application\Withdrawal\ManagerAction;

use Symfony\Component\Validator\Constraints as Assert;

final class ManagerActionCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'id is required')]
        #[Assert\Uuid(message: 'id must be a valid UUID')]
        public readonly string $id,

        #[Assert\NotBlank(message: 'managerId is required')]
        public readonly string $managerId,

        #[Assert\NotBlank(message: 'managerRole is required')]
        public readonly string $managerRole,
    ) {}
}
