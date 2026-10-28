<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Enums\ItemType;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class Consignment implements Arrayable, HasSerializableProperties
{
    use SerializesProperties;

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

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'ItemType' => $this->itemType,
            'ItemsAreStackable' => $this->itemsAreStackable,
            'ConsignmentSummary' => $this->consignmentSummary,
            'ConsignmentValue' => $this->consignmentValue,
            'ConsignmentCurrency' => $this->consignmentCurrency,
            'Packages' => $this->packages,
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
            itemType: isset($data['ItemType'])
                ? (ItemType::tryFrom($data['ItemType']) ?: $data['ItemType'])
                : null,
            itemsAreStackable: $data['ItemsAreStackable'] ?? null,
            consignmentSummary: $data['ConsignmentSummary'] ?? null,
            consignmentValue: $data['ConsignmentValue'] ?? null,
            consignmentCurrency: isset($data['ConsignmentCurrency'])
                ? ConsignmentCurrency::fromArray($data['ConsignmentCurrency'])
                : null,
            packages: isset($data['Packages'])
                ? array_map(
                    static fn ($package): Package => Package::fromArray($package),
                    $data['Packages'],
                )
                : null,
        );
    }
}
