<?php

namespace App\Infrastructure\Http;

use Symfony\Contracts\HttpClient\HttpClientInterface;

final class PlayersServiceClient extends BaseHttpClient
{
    public function __construct(
        HttpClientInterface $httpClient,
        string $baseUrl,
        int $timeout = 10
    ) {
        parent::__construct($httpClient, $baseUrl, $timeout);
    }

    /**
     * GET /users/players - Список игроков
     *
     * @param array{
     *     offset?: int,
     *     limit?: int,
     *     search?: string,
     *     country?: string,
     *     currency?: string,
     *     sortBy?: string,
     *     order?: string
     * } $queryParams
     * @return array{items: array<int, array<string, mixed>>, total: int}
     */
    public function listPlayers(array $queryParams = []): array
    {
        $queryString = http_build_query(array_filter($queryParams, fn($v) => $v !== null && $v !== ''));
        $endpoint = '/users/players' . ($queryString ? '?' . $queryString : '');

        $response = $this->request('GET', $endpoint);
        return $this->decodeResponse($response);
    }

    /**
     * POST /users/players - Создать игрока (admin)
     *
     * @param array{
     *     login: string,
     *     password: string,
     *     country: string,
     *     currency: string,
     *     promoCode?: string
     * } $data
     * @return bool
     */
    public function createPlayer(array $data): bool
    {
        $response = $this->request('POST', '/users/players', [
            'json' => $data,
        ]);

        $result = $this->decodeResponse($response);
        return $result['success'] ?? true;
    }

    /**
     * GET /users/players/{id} - Получить игрока по ID
     *
     * @return array<string, mixed>
     */
    public function getPlayer(string $id): array
    {
        $response = $this->request('GET', "/users/players/{$id}");
        return $this->decodeResponse($response);
    }

    /**
     * POST /users/players/getInfo - Получить информацию о нескольких игроках
     *
     * @param string[] $ids
     * @return array<int, array<string, mixed>>
     */
    public function getPlayersInfo(array $ids): array
    {
        $response = $this->request('POST', '/users/players/getInfo', [
            'json' => ['ids' => $ids],
        ]);

        return $this->decodeResponse($response);
    }

    /**
     * PUT /users/players/{id}/update - Обновить профиль игрока
     *
     * @param array{
     *     login?: string,
     *     email?: string,
     *     phone?: string,
     *     name?: string,
     *     surname?: string,
     *     nickname?: string,
     *     currency?: string,
     *     country?: string
     * } $data
     * @return bool
     */
    public function updatePlayer(string $id, array $data): bool
    {
        $response = $this->request('PUT', "/users/players/{$id}/update", [
            'json' => $data,
        ]);

        $result = $this->decodeResponse($response);
        return $result['success'] ?? true;
    }

    /**
     * PUT /users/players/{id}/update/pass - Изменить пароль
     *
     * @return bool
     */
    public function updatePassword(string $id, string $password): bool
    {
        $response = $this->request('PUT', "/users/players/{$id}/update/pass", [
            'json' => ['password' => $password],
        ]);

        $result = $this->decodeResponse($response);
        return $result['success'] ?? true;
    }

    /**
     * PUT /users/players/{id}/ban - Заблокировать игрока
     *
     * @return bool
     */
    public function banPlayer(string $id): bool
    {
        $response = $this->request('PUT', "/users/players/{$id}/ban");
        $result = $this->decodeResponse($response);
        return $result['success'] ?? true;
    }

    /**
     * PUT /users/players/{id}/unban - Разблокировать игрока
     *
     * @return bool
     */
    public function unbanPlayer(string $id): bool
    {
        $response = $this->request('PUT', "/users/players/{$id}/unban");
        $result = $this->decodeResponse($response);
        return $result['success'] ?? true;
    }

    /**
     * GET /users/players/{id}/documents - Получить документы игрока
     *
     * @return array<string, mixed>
     */
    public function getPlayerDocuments(string $id): array
    {
        $response = $this->request('GET', "/users/players/{$id}/documents");
        return $this->decodeResponse($response);
    }

    /**
     * PATCH /users/players/document/{id} - Изменить статус документа
     *
     * @param array{status: string} $data
     * @return bool
     */
    public function updatePlayerDocumentStatus(string $id, array $data): bool
    {
        $response = $this->request('PATCH', "/users/players/document/{$id}", [
            'json' => $data,
        ]);
        $result = $this->decodeResponse($response);
        return $result['success'] ?? true;
    }

    /**
     * PUT /users/players/{id}/update/level - Изменить уровень игрока
     *
     * @param array<string, mixed> $data
     * @return bool
     */
    public function updatePlayerLevel(string $id, array $data): bool
    {
        $response = $this->request('PUT', "/users/players/{$id}/update/level", [
            'json' => $data,
        ]);
        $result = $this->decodeResponse($response);
        return $result['success'] ?? true;
    }

    /**
     * POST /users/players/kick - Принудительный выход игроков
     *
     * @param string[] $ids
     * @return bool
     */
    public function kickPlayers(array $ids): bool
    {
        $response = $this->request('POST', '/users/players/kick', [
            'json' => $ids,
        ]);
        $result = $this->decodeResponse($response);
        return $result['success'] ?? true;
    }

    /**
     * GET /finances/player-requisites-v2/{id} - Получить реквизиты игрока
     *
     * @return array<string, mixed>
     */
    public function getPlayerRequisites(string $id): array
    {
        $response = $this->request('GET', "/finances/player-requisites-v2/{$id}");
        return $this->decodeResponse($response);
    }

    /**
     * POST /finances/player-requisites-v2/{id} - Изменить реквизиты игрока
     *
     * @param array<string, mixed> $data
     * @return bool
     */
    public function updatePlayerRequisites(string $id, array $data): bool
    {
        $response = $this->request('POST', "/finances/player-requisites-v2/{$id}", [
            'json' => $data,
        ]);
        $result = $this->decodeResponse($response);
        return $result['success'] ?? true;
    }
}
