<?php

namespace App\Application\Player\KickPlayers;

use App\Infrastructure\Http\PlayersServiceClient;

final class KickPlayersHandler
{
    public function __construct(private PlayersServiceClient $playersService) {}

    public function handle(KickPlayersCommand $command): bool
    {
        return $this->playersService->kickPlayers($command->ids);
    }
}
