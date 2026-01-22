<?php

namespace App\Application\Player\Dto;

final class ListPlayersResultDto
{
    /**
     * @param PlayerDto[] $items
     */
    public function __construct(
        public readonly array $items,
        public readonly int $total,
    ) {}

    /**
     * @param array{items: array<int, array<string, mixed>>, total: int} $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $items = array_map(
            fn(array $item) => PlayerDto::fromArray($item),
            $data['items'] ?? []
        );

        return new self(
            items: $items,
            total: (int)($data['total'] ?? 0),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'items' => array_map(fn(PlayerDto $player) => $player->toArray(), $this->items),
            'total' => $this->total,
        ];
    }
}
