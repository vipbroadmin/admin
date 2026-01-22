<?php

namespace App\Application\Player\UnbanPlayer;

use App\Infrastructure\Http\PlayersServiceClient;

final class UnbanPlayerHandler
{
    public function __construct(
        private PlayersServiceClient $playersService
    ) {}

    public function handle(UnbanPlayerCommand $command): bool
    {
        return $this->playersService->unbanPlayer($command->id);
    }
}
