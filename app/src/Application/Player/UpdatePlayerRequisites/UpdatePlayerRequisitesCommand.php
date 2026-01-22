<?php

namespace App\Application\Player\UpdatePlayerRequisites;

use Symfony\Component\Validator\Constraints as Assert;

final class UpdatePlayerRequisitesCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'id is required')]
        #[Assert\Uuid(message: 'id must be a valid UUID')]
        public readonly string $id,

        #[Assert\Type(type: 'string', message: 'paymentMethodId must be a string')]
        public readonly ?string $paymentMethodId = null,

        #[Assert\Type(type: 'array', message: 'formData must be an object')]
        public readonly ?array $formData = null,
    ) {}
}
