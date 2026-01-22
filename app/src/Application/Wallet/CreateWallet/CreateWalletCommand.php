<?php

namespace App\Application\Wallet\CreateWallet;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateWalletCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'playerId is required')]
        #[Assert\Uuid(message: 'playerId must be a valid UUID')]
        public readonly string $playerId,

        #[Assert\NotBlank(message: 'currency is required')]
        #[Assert\Type(type: 'string', message: 'currency must be a string')]
        public readonly string $currency,

        #[Assert\Type(type: 'string', message: 'type must be a string')]
        public readonly ?string $type = null,
    ) {}
}
