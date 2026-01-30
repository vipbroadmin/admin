<?php

namespace App\Application\Slotegrator\GetGames;

use App\Infrastructure\Http\SlotegratorServiceClient;

final class GetGamesHandler
{
    public function __construct(private SlotegratorServiceClient $client) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(GetGamesQuery $query): array
    {
        $params = array_filter([
            'provider_id' => $query->providerId,
            'status' => $query->status,
            'search' => $query->search,
            'limit' => $query->limit,
            'offset' => $query->offset,
        ], fn($value) => $value !== null && $value !== '');

        return $this->client->getGames($params);
    }
}
