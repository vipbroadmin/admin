<?php

namespace App\Application\Player\GetPlayersInfo;

use App\Application\Player\Dto\PlayerDto;
use App\Infrastructure\Http\PlayersServiceClient;

final class GetPlayersInfoHandler
{
    public function __construct(
        private PlayersServiceClient $playersService
    ) {}

    /**
     * @return PlayerDto[]
     */
    public function handle(GetPlayersInfoQuery $query): array
    {
        $data = $this->playersService->getPlayersInfo($query->ids);
        return array_map(fn(array $item) => PlayerDto::fromArray($item), $data);
    }
}
