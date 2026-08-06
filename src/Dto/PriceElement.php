<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class PriceElement implements Arrayable
{
    /**
     * @param string|null $code
     * @param string|null $description
     * @param float|null  $cost
     */
    public function __construct(
        readonly public ?string $code = null,
        readonly public ?string $description = null,
        readonly public ?float  $cost = null,
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'Code' => $this->code,
            'Description' => $this->description,
            'Cost' => $this->cost,
        ], static fn($value): bool => $value !== null);
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            code: $data['Code'] ?? null,
            description: $data['Description'] ?? null,
            cost: $data['Cost'] ?? null,
        );
    }
}
