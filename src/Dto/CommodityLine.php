<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class CommodityLine implements Arrayable
{
    public function __construct(
        readonly public ?string $commodityCode = null,
        readonly public ?string $commodityDescription = null,
        readonly public ?Country $countryOfOrigin = null,
        readonly public ?int $numberOfUnits = null,
        readonly public ?float $unitValue = null,
        readonly public ?float $unitWeight = null,
        readonly public ?string $productCode = null,
        readonly public ?Address $manufacturerAddress = null,
        readonly public ?CPSCDeclaration $cpscDeclaration = null,
    )
    {
    }

    public function toArray(): array
    {
        return array_filter([
            'CommodityCode' => $this->commodityCode,
            'CommodityDescription' => $this->commodityDescription,
            'CountryOfOrigin' => $this->countryOfOrigin?->toArray(),
            'NumberOfUnits' => $this->numberOfUnits,
            'UnitValue' => $this->unitValue,
            'UnitWeight' => $this->unitWeight,
            'ProductCode' => $this->productCode,
            'ManufacturerAddress' => $this->manufacturerAddress?->toArray(),
            'CpscDeclaration' => $this->cpscDeclaration?->toArray(),
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            commodityCode: $data['CommodityCode'] ?? null,
            commodityDescription: $data['CommodityDescription'] ?? null,
            countryOfOrigin: isset($data['CountryOfOrigin']) ? Country::fromArray($data['CountryOfOrigin']) : null,
            numberOfUnits: $data['NumberOfUnits'] ?? null,
            unitValue: $data['UnitValue'] ?? null,
            unitWeight: $data['UnitWeight'] ?? null,
            productCode: $data['ProductCode'] ?? null,
            manufacturerAddress: isset($data['ManufacturerAddress']) ? Address::fromArray($data['ManufacturerAddress']) : null,
            cpscDeclaration: isset($data['CpscDeclaration']) ? CPSCDeclaration::fromArray($data['CpscDeclaration']) : null,
        );
    }
}