<?php

namespace App\Application\Player\UpdatePlayerRequisites;

use App\Infrastructure\Http\PlayersServiceClient;

final class UpdatePlayerRequisitesHandler
{
    public function __construct(private PlayersServiceClient $playersService) {}

    public function handle(UpdatePlayerRequisitesCommand $command): bool
    {
        $payload = [];
        if ($command->paymentMethodId !== null) {
            $payload['paymentMethodId'] = $command->paymentMethodId;
        }
        if ($command->formData !== null) {
            $payload['formData'] = $command->formData;
        }

        return $this->playersService->updatePlayerRequisites($command->id, $payload);
    }
}
