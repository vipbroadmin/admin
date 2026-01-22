<?php

namespace App\Application\Player\UpdatePlayer;

use App\Infrastructure\Http\PlayersServiceClient;

final class UpdatePlayerHandler
{
    public function __construct(
        private PlayersServiceClient $playersService
    ) {}

    public function handle(UpdatePlayerCommand $command): bool
    {
        $data = array_filter([
            'login' => $command->login,
            'email' => $command->email,
            'phone' => $command->phone,
            'name' => $command->name,
            'surname' => $command->surname,
            'nickname' => $command->nickname,
            'currency' => $command->currency,
            'country' => $command->country,
        ], fn($value) => $value !== null);

        return $this->playersService->updatePlayer($command->id, $data);
    }
}
