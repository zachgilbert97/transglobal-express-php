<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class CommodityLine implements Arrayable, HasSerializableProperties
{
    use SerializesProperties;

    /**
     * @param string|null          $commodityCode
     * @param string|null          $commodityDescription
     * @param Country|null         $countryOfOrigin
     * @param int|null             $numberOfUnits
     * @param float|null           $unitValue
     * @param float|null           $unitWeight
     * @param string|null          $productCode
     * @param Address|null         $manufacturerAddress
     * @param CPSCDeclaration|null    $cpscDeclaration
     * @param ProductIdentifiers|null $productIdentifiers Required for B2C shipments to the EU under €150
     */
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
        readonly public ?ProductIdentifiers $productIdentifiers = null,
    )
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'CommodityCode' => $this->commodityCode,
            'CommodityDescription' => $this->commodityDescription,
            'CountryOfOrigin' => $this->countryOfOrigin,
            'NumberOfUnits' => $this->numberOfUnits,
            'UnitValue' => $this->unitValue,
            'UnitWeight' => $this->unitWeight,
            'ProductCode' => $this->productCode,
            'ManufacturerAddress' => $this->manufacturerAddress,
            'CpscDeclaration' => $this->cpscDeclaration,
            'ProductIdentifiers' => $this->productIdentifiers,
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
            commodityCode: $data['CommodityCode'] ?? null,
            commodityDescription: $data['CommodityDescription'] ?? null,
            countryOfOrigin: isset($data['CountryOfOrigin']) ? Country::fromArray($data['CountryOfOrigin']) : null,
            numberOfUnits: $data['NumberOfUnits'] ?? null,
            unitValue: $data['UnitValue'] ?? null,
            unitWeight: $data['UnitWeight'] ?? null,
            productCode: $data['ProductCode'] ?? null,
            manufacturerAddress: isset($data['ManufacturerAddress']) ? Address::fromArray($data['ManufacturerAddress']) : null,
            cpscDeclaration: isset($data['CpscDeclaration']) ? CPSCDeclaration::fromArray($data['CpscDeclaration']) : null,
            productIdentifiers: isset($data['ProductIdentifiers'])
                ? ProductIdentifiers::fromArray($data['ProductIdentifiers'])
                : null,
        );
    }
}
