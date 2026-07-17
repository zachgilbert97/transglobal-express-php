<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Enums\ItemType;
use ZachGilbert\TransglobalExpress\Traits\UnwrapsEnums;

final class Consignment implements Arrayable
{
    use UnwrapsEnums;

    /**
     * @param ItemType|string|null     $itemType
     * @param bool|null                $itemsAreStackable
     * @param string|null              $consignmentSummary
     * @param float|null               $consignmentValue
     * @param ConsignmentCurrency|null $consignmentCurrency
     * @param Package[]|null           $packages
     */
    public function __construct(
        readonly public ItemType|string|null $itemType = null,
        readonly public ?bool                $itemsAreStackable = null,
        readonly public ?string              $consignmentSummary = null,
        readonly public ?float               $consignmentValue = null,
        readonly public ?ConsignmentCurrency $consignmentCurrency = null,
        readonly public ?array               $packages = null,
    )
    {
    }

    public function toArray(): array
    {
        return array_filter([
            'ItemType' => $this->unwrapEnumValue($this->itemType),
            'ItemsAreStackable' => $this->itemsAreStackable,
            'ConsignmentSummary' => $this->consignmentSummary,
            'ConsignmentValue' => $this->consignmentValue,
            'ConsignmentCurrency' => $this->consignmentCurrency->toArray(),
            'Packages' => $this->packages,
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            itemType: isset($data['ItemType']) ? ItemType::from($data['ItemType']) : null,
            itemsAreStackable: $data['ItemsAreStackable'] ?? null,
            consignmentSummary: $data['ConsignmentSummary'] ?? null,
            consignmentValue: $data['ConsignmentValue'] ?? null,
            consignmentCurrency: isset($data['ConsignmentCurrency']) ? ConsignmentCurrency::fromArray($data['ConsignmentCurrency']) : null,
            packages: isset($data['Packages'])
                ? array_map(
                    fn ($package): Package => Package::fromArray($package),
                    $data['Packages'],
                )
                : null,
        );
    }
}