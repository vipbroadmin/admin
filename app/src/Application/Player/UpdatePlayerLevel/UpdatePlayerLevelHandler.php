<?php

namespace App\Application\Player\UpdatePlayerLevel;

use App\Infrastructure\Http\PlayersServiceClient;

final class UpdatePlayerLevelHandler
{
    public function __construct(private PlayersServiceClient $playersService) {}

    public function handle(UpdatePlayerLevelCommand $command): bool
    {
        return $this->playersService->updatePlayerLevel($command->id, [
            'level' => $command->level,
        ]);
    }
}
