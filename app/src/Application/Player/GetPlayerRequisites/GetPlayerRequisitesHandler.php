<?php

namespace App\Application\Player\GetPlayerRequisites;

use App\Infrastructure\Http\PlayersServiceClient;

final class GetPlayerRequisitesHandler
{
    public function __construct(private PlayersServiceClient $playersService) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(GetPlayerRequisitesQuery $query): array
    {
        return $this->playersService->getPlayerRequisites($query->id);
    }
}
