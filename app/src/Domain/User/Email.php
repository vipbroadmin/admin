<?php

namespace App\Domain\User;

final class Email
{
    private function __construct(private string $value) {}

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if ($value === '') {
            throw new \InvalidArgumentException('Email must not be empty.');
        }

        if (filter_var($value, FILTER_VALIDATE_EMAIL) === false) {
            throw new \InvalidArgumentException('Email must be a valid email address.');
        }

        return new self(mb_strtolower($value));
    }

    public function value(): string
    {
        return $this->value;
    }
}
