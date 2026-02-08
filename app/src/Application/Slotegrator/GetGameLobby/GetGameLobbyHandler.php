<?php

namespace App\Application\Slotegrator\GetGameLobby;

use App\Infrastructure\Http\SlotegratorServiceClient;

final class GetGameLobbyHandler
{
    public function __construct(private SlotegratorServiceClient $client) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(GetGameLobbyQuery $query): array
    {
        return $this->client->getLobby(
            $query->gameUuid,
            $query->currency,
            $query->technology
        );
    }
}
