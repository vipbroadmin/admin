<?php

namespace App\Application\Player\GetPlayerDocuments;

use App\Infrastructure\Http\PlayersServiceClient;

final class GetPlayerDocumentsHandler
{
    public function __construct(private PlayersServiceClient $playersService) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(GetPlayerDocumentsQuery $query): array
    {
        return $this->playersService->getPlayerDocuments($query->id);
    }
}
