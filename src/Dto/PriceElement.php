<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class PriceElement implements Arrayable, HasSerializableProperties
{
    use SerializesProperties;

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
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'Code' => $this->code,
            'Description' => $this->description,
            'Cost' => $this->cost,
        ];
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
