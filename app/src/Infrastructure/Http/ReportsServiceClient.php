<?php

namespace App\Infrastructure\Http;

final class ReportsServiceClient extends BaseHttpClient
{
    /**
     * GET /finances/reports/transactions
     *
     * @param array{
     *     playerId?: string,
     *     walletId?: string,
     *     currency?: string,
     *     type?: string,
     *     reason?: string,
     *     from?: string,
     *     to?: string,
     *     sortBy?: string,
     *     order?: string,
     *     limit?: int,
     *     offset?: int
     * } $queryParams
     * @return array<string, mixed>
     */
    public function listTransactions(array $queryParams = []): array
    {
        $queryString = http_build_query(array_filter(
            $queryParams,
            static fn($v) => $v !== null && $v !== ''
        ));
        $endpoint = '/finances/reports/transactions' . ($queryString ? '?' . $queryString : '');

        $response = $this->request('GET', $endpoint);
        return $this->decodeResponse($response);
    }
}
