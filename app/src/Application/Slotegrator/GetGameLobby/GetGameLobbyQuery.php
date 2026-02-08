<?php

namespace App\Application\Slotegrator\GetGameLobby;

use Symfony\Component\Validator\Constraints as Assert;

final class GetGameLobbyQuery
{
    public function __construct(
        #[Assert\NotBlank(message: 'game_uuid is required')]
        #[Assert\Type(type: 'string', message: 'game_uuid must be a string')]
        public readonly string $gameUuid,

        #[Assert\NotBlank(message: 'currency is required')]
        #[Assert\Type(type: 'string', message: 'currency must be a string')]
        public readonly string $currency,

        #[Assert\Type(type: 'string', message: 'technology must be a string')]
        public readonly ?string $technology = null,
    ) {}
}
