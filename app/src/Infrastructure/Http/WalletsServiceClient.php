<?php

namespace App\Infrastructure\Http;

final class WalletsServiceClient extends BaseHttpClient
{
    /**
     * GET /finances/wallets/by-player-id/{playerId}
     *
     * @return array{wallets: array<int, array<string, mixed>>}
     */
    public function getWalletsByPlayerId(string $playerId): array
    {
        $response = $this->request('GET', "/finances/wallets/by-player-id/{$playerId}");
        return $this->decodeResponse($response);
    }

    /**
     * POST /finances/wallets
     *
     * @param array{
     *     playerId: string,
     *     currency: string,
     *     type?: string
     * } $data
     * @return array<string, mixed>
     */
    public function createWallet(array $data): array
    {
        $response = $this->request('POST', '/finances/wallets', [
            'json' => $data,
        ]);

        return $this->decodeResponse($response);
    }

    /**
     * POST /finances/wallets/deposit
     *
     * @param array{
     *     playerId: string,
     *     walletId: string,
     *     amount: string
     * } $data
     * @return array<string, mixed>
     */
    public function deposit(array $data): array
    {
        $response = $this->request('POST', '/finances/wallets/deposit', [
            'json' => $data,
        ]);

        return $this->decodeResponse($response);
    }

    /**
     * POST /finances/wallets/cashout
     *
     * @param array{
     *     playerId: string,
     *     walletId: string,
     *     amount: string,
     *     paymentSystemId: string
     * } $data
     * @return array<string, mixed>
     */
    public function cashout(array $data): array
    {
        $response = $this->request('POST', '/finances/wallets/cashout', [
            'json' => $data,
        ]);

        return $this->decodeResponse($response);
    }

    /**
     * POST /finances/wallets/unlock
     *
     * @param array{
     *     playerId: string,
     *     walletId: string,
     *     amount: string
     * } $data
     * @return array<string, mixed>
     */
    public function unlock(array $data): array
    {
        $response = $this->request('POST', '/finances/wallets/unlock', [
            'json' => $data,
        ]);

        return $this->decodeResponse($response);
    }

    /**
     * POST /finances/wallets/confirm-withdrawal
     *
     * @param array{withdrawalRequestId: string} $data
     * @return array<string, mixed>
     */
    public function confirmWithdrawal(array $data): array
    {
        $response = $this->request('POST', '/finances/wallets/confirm-withdrawal', [
            'json' => $data,
        ]);

        return $this->decodeResponse($response);
    }
}
