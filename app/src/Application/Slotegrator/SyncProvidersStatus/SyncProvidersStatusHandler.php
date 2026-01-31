<?php

namespace App\Application\Slotegrator\SyncProvidersStatus;

use App\Infrastructure\Http\SlotegratorServiceClient;

final class SyncProvidersStatusHandler
{
    public function __construct(private SlotegratorServiceClient $client) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(SyncProvidersStatusQuery $query): array
    {
        $params = array_filter([
            'limit' => $query->limit,
            'offset' => $query->offset,
        ], static fn($value) => $value !== null && $value !== '');

        return $this->client->getProvidersSyncStatus($params);
    }
}
