<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class TotalCost implements Arrayable {
    public function __construct(
        readonly public ?float $totalCostNetWithCollection = null,
        readonly public ?float $totalCostNetWithoutCollection = null,
        readonly public ?float $totalCostGrossWithCollection = null,
        readonly public ?float $totalCostGrossWithoutCollection = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'TotalCostNetWithCollection' => $this->totalCostNetWithCollection,
            'TotalCostNetWithoutCollection' => $this->totalCostNetWithoutCollection,
            'TotalCostGrossWithCollection' => $this->totalCostGrossWithCollection,
            'TotalCostGrossWithoutCollection' => $this->totalCostGrossWithoutCollection,
        ], static fn ($value): bool => $value !== null);
    }

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
