<?php

namespace App\Domain\User;

final class UserName
{
    private function __construct(private string $value) {}

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if ($value === '') {
            throw new \InvalidArgumentException('Name must not be empty.');
        }
        if (mb_strlen($value) > 90) {
            throw new \InvalidArgumentException('Name must be at most 90 characters.');
        }

        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }
}
