<?php

namespace App\Domain\User;

final class UserId
{
    private function __construct(private int $value) {}

    public static function fromInt(int $value): self
    {
        if ($value <= 0) {
            throw new \InvalidArgumentException('UserId must be a positive integer.');
        }
        return new self($value);
    }

    public function value(): int
    {
        return $this->value;
    }
}
