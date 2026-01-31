<?php

namespace App\Application\Slotegrator\SyncProviders;

use App\Infrastructure\Http\SlotegratorServiceClient;

final class SyncProvidersHandler
{
    public function __construct(private SlotegratorServiceClient $client) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(SyncProvidersCommand $command): array
    {
        return $this->client->syncProviders();
    }
}
