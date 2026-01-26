<?php

namespace App\Application\Wallet\ConfirmWithdrawal;

use Symfony\Component\Validator\Constraints as Assert;

final class ConfirmWithdrawalCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'withdrawalRequestId is required')]
        #[Assert\Uuid(message: 'withdrawalRequestId must be a valid UUID')]
        public readonly string $withdrawalRequestId,
    ) {}
}
