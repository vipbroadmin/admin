<?php

namespace App\Application\User\ListUsers;

use Symfony\Component\Validator\Constraints as Assert;

final class ListUsersQuery
{
    public function __construct(
        #[Assert\Type(type: 'integer', message: 'offset must be an integer')]
        #[Assert\GreaterThanOrEqual(value: 0, message: 'offset must be >= 0')]
        public readonly ?int $offset = null,

        #[Assert\Type(type: 'integer', message: 'limit must be an integer')]
        #[Assert\Range(min: 1, max: 1000, notInRangeMessage: 'limit must be between 1 and 1000')]
        public readonly ?int $limit = null,
    ) {}
}
