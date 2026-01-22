<?php

namespace App\Application\Player\GetPlayer;

use App\Application\Player\Dto\PlayerDto;
use App\Infrastructure\Http\PlayersServiceClient;

final class GetPlayerHandler
{
    public function __construct(
        private PlayersServiceClient $playersService
    ) {}

    public function handle(GetPlayerQuery $query): ?PlayerDto
    {
        try {
            $data = $this->playersService->getPlayer($query->id);
            return PlayerDto::fromArray($data);
        } catch (\App\Infrastructure\Http\Exception\ExternalServiceNotFoundException) {
            return null;
        }
    }
}
