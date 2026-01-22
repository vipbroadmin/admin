<?php

namespace App\Application\Player\KickPlayers;

use Symfony\Component\Validator\Constraints as Assert;

final class KickPlayersCommand
{
    /**
     * @param string[] $ids
     */
    public function __construct(
        #[Assert\NotNull(message: 'ids is required')]
        #[Assert\Type(type: 'array', message: 'ids must be an array')]
        #[Assert\Count(min: 1, minMessage: 'ids must contain at least one item')]
        #[Assert\All([
            new Assert\Uuid(message: 'each id must be a valid UUID'),
        ])]
        public readonly array $ids,
    ) {}
}
