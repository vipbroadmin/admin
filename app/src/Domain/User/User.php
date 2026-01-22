<?php

namespace App\Domain\User;

final class User
{
    private function __construct(
        private UserId $id,
        private UserName $name,
        private Email $email,
    ) {}

    public static function create(UserId $id, UserName $name, Email $email): self
    {
        return new self($id, $name, $email);
    }

    public function id(): UserId
    {
        return $this->id;
    }

    public function name(): UserName
    {
        return $this->name;
    }

    public function email(): Email
    {
        return $this->email;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->value(),
            'name' => $this->name->value(),
            'email' => $this->email->value(),
        ];
    }
}
