<?php

namespace App\Application\Player\GetPlayersInfo;

use Symfony\Component\Validator\Constraints as Assert;

final class GetPlayersInfoQuery
{
    /**
     * @param string[] $ids
     */
    public function __construct(
        #[Assert\NotEmpty(message: 'ids are required')]
        #[Assert\All([
            new Assert\Uuid(message: 'Each id must be a valid UUID'),
        ])]
        public readonly array $ids
    ) {}
}
