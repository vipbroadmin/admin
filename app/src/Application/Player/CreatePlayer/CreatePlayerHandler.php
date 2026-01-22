<?php

namespace App\Application\Player\CreatePlayer;

use App\Infrastructure\Http\PlayersServiceClient;

final class CreatePlayerHandler
{
    public function __construct(
        private PlayersServiceClient $playersService
    ) {}

    public function handle(CreatePlayerCommand $command): bool
    {
        $data = [
            'login' => $command->login,
            'password' => $command->password,
            'country' => $command->country,
            'currency' => $command->currency,
        ];

        if ($command->promoCode !== null) {
            $data['promoCode'] = $command->promoCode;
        }

        return $this->playersService->createPlayer($data);
    }
}
