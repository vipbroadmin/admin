<?php

namespace App\Infrastructure\Http;

final class SlotegratorServiceClient extends BaseHttpClient
{
    /**
     * GET /slotegrator/providers
     *
     * @param array{
     *     status?: string,
     *     search?: string,
     *     limit?: int,
     *     offset?: int
     * } $query
     * @return array{items: array<int, array<string, mixed>>}
     */
    public function getProviders(array $query): array
    {
        $response = $this->request('GET', '/slotegrator/providers', [
            'query' => $query,
        ]);

        return $this->decodeResponse($response);
    }

    /**
     * POST /slotegrator/providers/sync
     *
     * @return array<string, mixed>
     */
    public function syncProviders(): array
    {
        $response = $this->request('POST', '/slotegrator/providers/sync');

        return $this->decodeResponse($response);
    }

    /**
     * GET /slotegrator/providers/sync/status
     *
     * @param array{
     *     limit?: int,
     *     offset?: int
     * } $query
     * @return array<string, mixed>
     */
    public function getProvidersSyncStatus(array $query): array
    {
        $response = $this->request('GET', '/slotegrator/providers/sync/status', [
            'query' => $query,
        ]);

        return $this->decodeResponse($response);
    }

    /**
     * GET /slotegrator/games
     *
     * @param array{
     *     provider_id?: int,
     *     status?: string,
     *     search?: string,
     *     limit?: int,
     *     offset?: int
     * } $query
     * @return array{items: array<int, array<string, mixed>>}
     */
    public function getGames(array $query): array
    {
        $response = $this->request('GET', '/slotegrator/games', [
            'query' => $query,
        ]);

        return $this->decodeResponse($response);
    }

    /**
     * POST /slotegrator/games/sync
     *
     * @param array{provider_ids?: array<int, int>} $data
     * @return array<string, mixed>
     */
    public function syncGames(array $data): array
    {
        $response = $this->request('POST', '/slotegrator/games/sync', [
            'json' => $data,
        ]);

        return $this->decodeResponse($response);
    }

    /**
     * GET /slotegrator/games/sync/status
     *
     * @param array{
     *     limit?: int,
     *     offset?: int
     * } $query
     * @return array<string, mixed>
     */
    public function getGamesSyncStatus(array $query): array
    {
        $response = $this->request('GET', '/slotegrator/games/sync/status', [
            'query' => $query,
        ]);

        return $this->decodeResponse($response);
    }

    /**
     * POST /slotegrator/launch
     *
     * @param array{
     *     game_uuid: string,
     *     player_id: string,
     *     player_name: string,
     *     currency: string,
     *     session_id?: string,
     *     device?: string,
     *     return_url?: string,
     *     language?: string,
     *     email?: string,
     *     lobby_data?: string,
     *     demo?: bool
     * } $data
     * @return array<string, mixed>
     */
    public function launch(array $data): array
    {
        $response = $this->request('POST', '/slotegrator/launch', [
            'json' => $data,
        ]);

        return $this->decodeResponse($response);
    }
}
