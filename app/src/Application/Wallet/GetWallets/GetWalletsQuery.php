<?php

namespace App\Application\Wallet\GetWallets;

use Symfony\Component\Validator\Constraints as Assert;

final class GetWalletsQuery
{
    public function __construct(
        #[Assert\NotBlank(message: 'playerId is required')]
        #[Assert\Uuid(message: 'playerId must be a valid UUID')]
        public readonly string $playerId,
    ) {}
}
