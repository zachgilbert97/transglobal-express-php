<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class PriceElement implements Arrayable
{
    public function __construct(
        readonly public ?string $code = null,
        readonly public ?string $description = null,
        readonly public ?float  $cost = null,
    )
    {
    }

    public function toArray(): array
    {
        return array_filter([
            'Code' => $this->code,
            'Description' => $this->description,
            'Cost' => $this->cost,
        ], static fn($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            code: $data['Code'] ?? null,
            description: $data['Description'] ?? null,
            cost: $data['Cost'] ?? null,
        );
    }
}