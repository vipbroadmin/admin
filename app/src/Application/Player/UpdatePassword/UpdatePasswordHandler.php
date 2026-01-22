<?php

namespace App\Application\Player\UpdatePassword;

use App\Infrastructure\Http\PlayersServiceClient;

final class UpdatePasswordHandler
{
    public function __construct(
        private PlayersServiceClient $playersService
    ) {}

    public function handle(UpdatePasswordCommand $command): bool
    {
        return $this->playersService->updatePassword($command->id, $command->password);
    }
}
