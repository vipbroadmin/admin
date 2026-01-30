<?php

namespace App\Application\Slotegrator\GetProviders;

use App\Infrastructure\Http\SlotegratorServiceClient;

final class GetProvidersHandler
{
    public function __construct(private SlotegratorServiceClient $client) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(GetProvidersQuery $query): array
    {
        $params = array_filter([
            'status' => $query->status,
            'search' => $query->search,
            'limit' => $query->limit,
            'offset' => $query->offset,
        ], fn($value) => $value !== null && $value !== '');

        return $this->client->getProviders($params);
    }
}
