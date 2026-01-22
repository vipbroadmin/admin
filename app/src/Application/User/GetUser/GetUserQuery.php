<?php

namespace App\Application\User\GetUser;

use Symfony\Component\Validator\Constraints as Assert;

final class GetUserQuery
{
    public function __construct(
        #[Assert\NotNull(message: 'id is required')]
        #[Assert\Type(type: 'integer', message: 'id must be an integer')]
        #[Assert\Positive(message: 'id must be a positive integer')]
        public mixed $id
    ) {}
}
