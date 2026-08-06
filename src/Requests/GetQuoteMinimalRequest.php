<?php

namespace ZachGilbert\TransglobalExpress\Requests;

use ZachGilbert\TransglobalExpress\Concerns\Request;
use ZachGilbert\TransglobalExpress\Dto\Address;
use ZachGilbert\TransglobalExpress\Dto\Consignment;
use ZachGilbert\TransglobalExpress\Dto\Package;
use ZachGilbert\TransglobalExpress\Dto\Shipment;
use ZachGilbert\TransglobalExpress\Traits\UnwrapsEnums;

class GetQuoteMinimalRequest implements Request
{
    use UnwrapsEnums;

    /**
     * @param Shipment $shipment
     */
    public function __construct(
        private readonly Shipment $shipment,
    ) {
    }

    /**
     * @return string
     */
    public function method(): string
    {
        return 'POST';
    }

    /**
     * @return string
     */
    public function uri(): string
    {
        return '/Quote/V2/GetQuoteMinimal';
    }

    /**
     * @return array
     */
    public function body(): array
    {
        return [
            'Shipment' => array_filter([
                'Consignment' => $this->shipment->consignment
                    ? $this->minimalConsignmentArray($this->shipment->consignment)
                    : null,

                'CollectionAddress' => $this->shipment->collectionAddress
                    ? $this->minimalAddressArray($this->shipment->collectionAddress)
                    : null,

                'DeliveryAddress' => $this->shipment->deliveryAddress
                    ? $this->minimalAddressArray($this->shipment->deliveryAddress)
                    : null,
            ]),
        ];
    }

    /**
     * @param Consignment $consignment
     *
     * @return array|null
     */
    private function minimalConsignmentArray(Consignment $consignment): ?array
    {
        return array_filter([
            'ItemType' => $this->unwrapEnumValue($consignment->itemType),
            'Packages' => $consignment->packages
                ? array_map(
                    fn (Package $package) => $this->minimalPackageArray($package),
                    $consignment->packages,
                )
                : null,
        ], static fn ($value): bool => !is_null($value));
    }

    /**
     * @param Package $package
     *
     * @return array|null
     */
    private function minimalPackageArray(Package $package): ?array
    {
        return array_filter([
            'Weight' => $package->weight,
            'Length' => $package->length,
            'Width'  => $package->width,
            'Height' => $package->height,
        ], static fn ($value): bool => !is_null($value));
    }

    /**
     * @param Address $address
     *
     * @return array|null
     */
    private function minimalAddressArray(Address $address): ?array
    {
        return array_filter([
            'City' => $address->city,
            'Postcode' => $address->postcode,
            'Country' => $address->country?->toArray(),
        ], static fn ($value): bool => !is_null($value));
    }
}
