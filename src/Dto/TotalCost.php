<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class TotalCost implements Arrayable, HasSerializableProperties {
    use SerializesProperties;

    /**
     * @param float|null $totalCostNetWithCollection
     * @param float|null $totalCostNetWithoutCollection
     * @param float|null $totalCostGrossWithCollection
     * @param float|null $totalCostGrossWithoutCollection
     */
    public function __construct(
        readonly public ?float $totalCostNetWithCollection = null,
        readonly public ?float $totalCostNetWithoutCollection = null,
        readonly public ?float $totalCostGrossWithCollection = null,
        readonly public ?float $totalCostGrossWithoutCollection = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'TotalCostNetWithCollection' => $this->totalCostNetWithCollection,
            'TotalCostNetWithoutCollection' => $this->totalCostNetWithoutCollection,
            'TotalCostGrossWithCollection' => $this->totalCostGrossWithCollection,
            'TotalCostGrossWithoutCollection' => $this->totalCostGrossWithoutCollection,
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
            totalCostNetWithCollection: $data['TotalCostNetWithCollection'] ?? null,
            totalCostNetWithoutCollection: $data['TotalCostNetWithoutCollection'] ?? null,
            totalCostGrossWithCollection: $data['TotalCostGrossWithCollection'] ?? null,
            totalCostGrossWithoutCollection: $data['TotalCostGrossWithoutCollection'] ?? null,
        );
    }
}
