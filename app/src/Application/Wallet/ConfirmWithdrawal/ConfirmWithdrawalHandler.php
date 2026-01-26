<?php

namespace App\Application\Wallet\ConfirmWithdrawal;

use App\Infrastructure\Http\WalletsServiceClient;

final class ConfirmWithdrawalHandler
{
    public function __construct(private WalletsServiceClient $walletsService) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(ConfirmWithdrawalCommand $command): array
    {
        return $this->walletsService->confirmWithdrawal([
            'withdrawalRequestId' => $command->withdrawalRequestId,
        ]);
    }
}
