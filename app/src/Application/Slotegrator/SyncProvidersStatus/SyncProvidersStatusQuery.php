<?php

namespace App\Application\Slotegrator\SyncProvidersStatus;

use Symfony\Component\Validator\Constraints as Assert;

final class SyncProvidersStatusQuery
{
    public function __construct(
        #[Assert\Type(type: 'integer', message: 'limit must be an integer')]
        #[Assert\GreaterThanOrEqual(value: 1, message: 'limit must be at least 1')]
        public mixed $limit,

        #[Assert\Type(type: 'integer', message: 'offset must be an integer')]
        #[Assert\GreaterThanOrEqual(value: 0, message: 'offset must be at least 0')]
        public mixed $offset,
    ) {}
}
