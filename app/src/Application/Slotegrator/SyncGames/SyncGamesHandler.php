<?php

namespace App\Application\Slotegrator\SyncGames;

use App\Infrastructure\Http\SlotegratorServiceClient;

final class SyncGamesHandler
{
    public function __construct(private SlotegratorServiceClient $client) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(SyncGamesCommand $command): array
    {
        $payload = [];
        if ($command->providerIds !== null) {
            $payload['provider_ids'] = $command->providerIds;
        }

        return $this->client->syncGames($payload);
    }
}
