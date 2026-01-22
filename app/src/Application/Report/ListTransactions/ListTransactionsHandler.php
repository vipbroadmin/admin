<?php

namespace App\Application\Report\ListTransactions;

use App\Infrastructure\Http\ReportsServiceClient;

final class ListTransactionsHandler
{
    public function __construct(private ReportsServiceClient $reportsService) {}

    /**
     * @return array<string, mixed>
     */
    public function handle(ListTransactionsQuery $query): array
    {
        return $this->reportsService->listTransactions([
            'playerId' => $query->playerId,
            'walletId' => $query->walletId,
            'currency' => $query->currency,
            'type' => $query->type,
            'reason' => $query->reason,
            'from' => $query->from,
            'to' => $query->to,
            'sortBy' => $query->sortBy,
            'order' => $query->order,
            'limit' => $query->limit,
            'offset' => $query->offset,
        ]);
    }
}
