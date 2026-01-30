<?php

namespace App\Application\Slotegrator\LaunchGame;

use App\Infrastructure\Http\SlotegratorServiceClient;

final class LaunchGameHandler
{
    public function __construct(private SlotegratorServiceClient $client) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(LaunchGameCommand $command): array
    {
        $payload = array_filter([
            'game_uuid' => $command->gameUuid,
            'player_id' => $command->playerId,
            'player_name' => $command->playerName,
            'currency' => $command->currency,
            'session_id' => $command->sessionId,
            'device' => $command->device,
            'return_url' => $command->returnUrl,
            'language' => $command->language,
            'email' => $command->email,
            'lobby_data' => $command->lobbyData,
            'demo' => $command->demo,
        ], static fn($value) => $value !== null && $value !== '');

        return $this->client->launch($payload);
    }
}
