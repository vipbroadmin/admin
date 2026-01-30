<?php

namespace App\Application\Slotegrator\LaunchGame;

use Symfony\Component\Validator\Constraints as Assert;

final class LaunchGameCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'game_uuid is required')]
        #[Assert\Type(type: 'string', message: 'game_uuid must be a string')]
        public mixed $gameUuid,

        #[Assert\NotBlank(message: 'player_id is required')]
        #[Assert\Type(type: 'string', message: 'player_id must be a string')]
        public mixed $playerId,

        #[Assert\NotBlank(message: 'player_name is required')]
        #[Assert\Type(type: 'string', message: 'player_name must be a string')]
        public mixed $playerName,

        #[Assert\NotBlank(message: 'currency is required')]
        #[Assert\Type(type: 'string', message: 'currency must be a string')]
        public mixed $currency,

        #[Assert\Type(type: 'string', message: 'session_id must be a string')]
        public mixed $sessionId,

        #[Assert\Choice(choices: ['desktop', 'mobile'], message: 'device must be desktop or mobile')]
        public mixed $device,

        #[Assert\Type(type: 'string', message: 'return_url must be a string')]
        public mixed $returnUrl,

        #[Assert\Type(type: 'string', message: 'language must be a string')]
        public mixed $language,

        #[Assert\Email(message: 'email must be a valid email address')]
        public mixed $email,

        #[Assert\Type(type: 'string', message: 'lobby_data must be a string')]
        public mixed $lobbyData,

        #[Assert\Type(type: 'bool', message: 'demo must be a boolean')]
        public mixed $demo,
    ) {}
}
