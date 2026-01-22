<?php

namespace App\Application\Player\ListPlayers;

use Symfony\Component\Validator\Constraints as Assert;

final class ListPlayersQuery
{
    public function __construct(
        #[Assert\Type(type: 'integer', message: 'offset must be an integer')]
        #[Assert\GreaterThanOrEqual(value: 0, message: 'offset must be >= 0')]
        public readonly ?int $offset = null,

        #[Assert\Type(type: 'integer', message: 'limit must be an integer')]
        #[Assert\Range(min: 1, max: 1000, notInRangeMessage: 'limit must be between 1 and 1000')]
        public readonly ?int $limit = null,

        public readonly ?string $search = null,
        public readonly ?string $country = null,
        public readonly ?string $currency = null,
        public readonly ?string $sortBy = null,

        #[Assert\Choice(choices: ['asc', 'desc'], message: 'order must be asc or desc')]
        public readonly ?string $order = null,
    ) {}
}
