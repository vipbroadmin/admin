<?php

namespace App\Application\Withdrawal\CreateWithdrawal;

use Symfony\Component\Validator\Constraints as Assert;

final class CreateWithdrawalCommand
{
    public function __construct(
        #[Assert\NotBlank(message: 'userId is required')]
        #[Assert\Uuid(message: 'userId must be a valid UUID')]
        public readonly string $userId,

        #[Assert\NotBlank(message: 'currency is required')]
        #[Assert\Type(type: 'string', message: 'currency must be a string')]
        public readonly string $currency,

        #[Assert\NotBlank(message: 'amount is required')]
        public readonly string $amount,

        #[Assert\NotBlank(message: 'paymentSystemId is required')]
        #[Assert\Type(type: 'string', message: 'paymentSystemId must be a string')]
        public readonly string $paymentSystemId,
    ) {}
}
