<?php

namespace App\Application\Slotegrator\GetGames;

use Symfony\Component\Validator\Constraints as Assert;

final class GetGamesQuery
{
    public function __construct(
        #[Assert\Type(type: 'integer', message: 'provider_id must be an integer')]
        #[Assert\GreaterThanOrEqual(value: 1, message: 'provider_id must be at least 1')]
        public mixed $providerId,

        #[Assert\Type(type: 'string', message: 'status must be a string')]
        public mixed $status,

        #[Assert\Type(type: 'string', message: 'search must be a string')]
        public mixed $search,

        #[Assert\Type(type: 'integer', message: 'limit must be an integer')]
        #[Assert\GreaterThanOrEqual(value: 1, message: 'limit must be at least 1')]
        public mixed $limit,

        #[Assert\Type(type: 'integer', message: 'offset must be an integer')]
        #[Assert\GreaterThanOrEqual(value: 0, message: 'offset must be at least 0')]
        public mixed $offset,
    ) {}
}
