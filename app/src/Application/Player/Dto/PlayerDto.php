<?php

namespace App\Application\Player\Dto;

final class PlayerDto
{
    public function __construct(
        public readonly string $id,
        public readonly ?string $login,
        public readonly ?string $email,
        public readonly ?string $phone,
        public readonly ?string $name,
        public readonly ?string $surname,
        public readonly ?string $nickname,
        public readonly ?string $currency,
        public readonly ?string $country,
        public readonly bool $isBanned,
        public readonly ?int $level,
        public readonly ?string $createdAt,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (string)($data['id'] ?? ''),
            login: isset($data['login']) ? (string)$data['login'] : null,
            email: isset($data['email']) ? (string)$data['email'] : null,
            phone: isset($data['phone']) ? (string)$data['phone'] : null,
            name: isset($data['name']) ? (string)$data['name'] : null,
            surname: isset($data['surname']) ? (string)$data['surname'] : null,
            nickname: isset($data['nickname']) ? (string)$data['nickname'] : null,
            currency: isset($data['currency']) ? (string)$data['currency'] : null,
            country: isset($data['country']) ? (string)$data['country'] : null,
            isBanned: (bool)($data['isBanned'] ?? false),
            level: isset($data['level']) ? (int)$data['level'] : null,
            createdAt: isset($data['createdAt']) ? (string)$data['createdAt'] : null,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'login' => $this->login,
            'email' => $this->email,
            'phone' => $this->phone,
            'name' => $this->name,
            'surname' => $this->surname,
            'nickname' => $this->nickname,
            'currency' => $this->currency,
            'country' => $this->country,
            'isBanned' => $this->isBanned,
            'level' => $this->level,
            'createdAt' => $this->createdAt,
        ];
    }
}
