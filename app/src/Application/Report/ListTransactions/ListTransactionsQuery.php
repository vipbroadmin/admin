<?php

namespace App\Application\Report\ListTransactions;

use Symfony\Component\Validator\Constraints as Assert;

final class ListTransactionsQuery
{
    public function __construct(
        #[Assert\Uuid(message: 'playerId must be a valid UUID')]
        public readonly ?string $playerId = null,

        #[Assert\Uuid(message: 'walletId must be a valid UUID')]
        public readonly ?string $walletId = null,

        public readonly ?string $currency = null,

        public readonly ?string $type = null,

        public readonly ?string $reason = null,

        public readonly ?string $from = null,

        public readonly ?string $to = null,

        #[Assert\Choice(choices: ['created_at', 'amount', 'balance_after'], message: 'sortBy must be created_at, amount or balance_after')]
        public readonly ?string $sortBy = null,

        #[Assert\Choice(choices: ['asc', 'desc', 'ASC', 'DESC'], message: 'order must be asc or desc')]
        public readonly ?string $order = null,

        #[Assert\Type(type: 'integer', message: 'limit must be an integer')]
        #[Assert\GreaterThanOrEqual(value: 0, message: 'limit must be >= 0')]
        public readonly ?int $limit = null,

        #[Assert\Type(type: 'integer', message: 'offset must be an integer')]
        #[Assert\GreaterThanOrEqual(value: 0, message: 'offset must be >= 0')]
        public readonly ?int $offset = null,
    ) {}
}
