<?php

namespace App\Application\Player\ListPlayers;

use App\Application\Player\Dto\ListPlayersResultDto;
use App\Infrastructure\Http\PlayersServiceClient;

final class ListPlayersHandler
{
    public function __construct(
        private PlayersServiceClient $playersService
    ) {}

    public function handle(ListPlayersQuery $query): ListPlayersResultDto
    {
        $params = array_filter([
            'offset' => $query->offset,
            'limit' => $query->limit,
            'search' => $query->search,
            'country' => $query->country,
            'currency' => $query->currency,
            'sortBy' => $query->sortBy,
            'order' => $query->order,
        ], fn($value) => $value !== null && $value !== '');

        $data = $this->playersService->listPlayers($params);
        return ListPlayersResultDto::fromArray($data);
    }
}
