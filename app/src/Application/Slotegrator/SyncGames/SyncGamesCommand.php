<?php

namespace App\Application\Slotegrator\SyncGames;

use Symfony\Component\Validator\Constraints as Assert;

final class SyncGamesCommand
{
    public function __construct(
        #[Assert\Optional([
            new Assert\Type(type: 'array', message: 'provider_ids must be an array'),
            new Assert\Count(min: 1, minMessage: 'provider_ids must contain at least one item'),
            new Assert\All([
                new Assert\Type(type: 'integer', message: 'provider_ids must contain integers'),
                new Assert\GreaterThanOrEqual(value: 1, message: 'provider_ids must be at least 1'),
            ]),
        ])]
        public mixed $providerIds,
    ) {}
}
