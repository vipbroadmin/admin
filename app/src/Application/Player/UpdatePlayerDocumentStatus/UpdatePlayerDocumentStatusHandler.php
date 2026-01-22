<?php

namespace App\Application\Player\UpdatePlayerDocumentStatus;

use App\Infrastructure\Http\PlayersServiceClient;

final class UpdatePlayerDocumentStatusHandler
{
    public function __construct(private PlayersServiceClient $playersService) {}

    public function handle(UpdatePlayerDocumentStatusCommand $command): bool
    {
        return $this->playersService->updatePlayerDocumentStatus($command->id, [
            'status' => $command->status,
        ]);
    }
}
