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
