<?php

namespace App\Infrastructure\Http;

final class WithdrawalsServiceClient extends BaseHttpClient
{
    /**
     * GET /finances/withdrawal-requests/{id}
     *
     * @return array<string, mixed>
     */
    public function getById(string $id): array
    {
        $response = $this->request('GET', "/finances/withdrawal-requests/{$id}");
        return $this->decodeResponse($response);
    }

    /**
     * POST /finances/withdrawal-requests
     *
     * @param array{
     *     playerId: string,
     *     walletId: string,
     *     currency: string,
     *     amount: string,
     *     paymentSystemId: string
     * } $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $response = $this->request('POST', '/finances/withdrawal-requests', [
            'json' => $data,
        ]);

        return $this->decodeResponse($response);
    }

    /**
     * POST /finances/withdrawal-requests/{id}/take
     *
     * @return array<string, mixed>
     */
    public function take(string $id, string $managerId, string $managerRole): array
    {
        return $this->postWithManagerHeaders("/finances/withdrawal-requests/{$id}/take", $managerId, $managerRole);
    }

    /**
     * POST /finances/withdrawal-requests/{id}/request-verification
     *
     * @return array<string, mixed>
     */
    public function requestVerification(string $id, string $managerId, string $managerRole): array
    {
        return $this->postWithManagerHeaders(
            "/finances/withdrawal-requests/{$id}/request-verification",
            $managerId,
            $managerRole
        );
    }

    /**
     * POST /finances/withdrawal-requests/{id}/approve
     *
     * @return array<string, mixed>
     */
    public function approve(string $id, string $managerId, string $managerRole): array
    {
        return $this->postWithManagerHeaders("/finances/withdrawal-requests/{$id}/approve", $managerId, $managerRole);
    }

    /**
     * POST /finances/withdrawal-requests/{id}/reject
     *
     * @return array<string, mixed>
     */
    public function reject(string $id, string $managerId, string $managerRole, string $reason): array
    {
        return $this->postWithManagerHeaders(
            "/finances/withdrawal-requests/{$id}/reject",
            $managerId,
            $managerRole,
            ['reason' => $reason]
        );
    }

    /**
     * POST /finances/withdrawal-requests/{id}/retry
     *
     * @return array<string, mixed>
     */
    public function retry(string $id, string $managerId, string $managerRole): array
    {
        return $this->postWithManagerHeaders("/finances/withdrawal-requests/{$id}/retry", $managerId, $managerRole);
    }

    /**
     * POST /finances/withdrawal-requests/{id}/return
     *
     * @return array<string, mixed>
     */
    public function returnRequest(string $id, string $managerId, string $managerRole): array
    {
        return $this->postWithManagerHeaders("/finances/withdrawal-requests/{$id}/return", $managerId, $managerRole);
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    private function postWithManagerHeaders(
        string $endpoint,
        string $managerId,
        string $managerRole,
        array $payload = []
    ): array {
        $options = [
            'headers' => [
                'X-Manager-Id' => $managerId,
                'X-Manager-Role' => $managerRole,
            ],
        ];

        if ($payload !== []) {
            $options['json'] = $payload;
        }

        $response = $this->request('POST', $endpoint, $options);
        return $this->decodeResponse($response);
    }
}
