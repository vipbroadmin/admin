<?php

namespace App\Application\Player\BanPlayer;

use App\Infrastructure\Http\PlayersServiceClient;

final class BanPlayerHandler
{
    public function __construct(
        private PlayersServiceClient $playersService
    ) {}

    public function handle(BanPlayerCommand $command): bool
    {
        return $this->playersService->banPlayer($command->id);
    }
}
