<?php

namespace App\Application\Slotegrator\SyncGamesStatus;

use App\Infrastructure\Http\SlotegratorServiceClient;

final class SyncGamesStatusHandler
{
    public function __construct(private SlotegratorServiceClient $client) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(SyncGamesStatusQuery $query): array
    {
        $params = array_filter([
            'limit' => $query->limit,
            'offset' => $query->offset,
        ], static fn($value) => $value !== null && $value !== '');

        return $this->client->getGamesSyncStatus($params);
    }
}
