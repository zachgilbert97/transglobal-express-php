<?php

namespace ZachGilbert\TransglobalExpress\Requests;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\Request;
use ZachGilbert\TransglobalExpress\Dto\Address;
use ZachGilbert\TransglobalExpress\Dto\Consignment;
use ZachGilbert\TransglobalExpress\Dto\Package;
use ZachGilbert\TransglobalExpress\Dto\Shipment;

class GetQuoteMinimalRequest implements Request
{
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
     * @return array<string, mixed>
     */
    public function body(): array
    {
        return [
            'Shipment' => new Shipment(
                consignment: $this->minimalConsignment($this->shipment->consignment),
                collectionAddress: $this->minimalAddress($this->shipment->collectionAddress),
                deliveryAddress: $this->minimalAddress($this->shipment->deliveryAddress),
            ),
        ];
    }

    /**
     * @param Consignment|null $consignment
     *
     * @return Consignment|null Null when the consignment has none of the minimal fields, so it is omitted
     */
    private function minimalConsignment(?Consignment $consignment): ?Consignment
    {
        if (is_null($consignment)) {
            return null;
        }

        return $this->nullIfEmpty(new Consignment(
            itemType: $consignment->itemType,
            packages: !empty($consignment->packages)
                ? array_map(
                    fn (Package $package): Package => $this->minimalPackage($package),
                    $consignment->packages,
                )
                : null,
        ));
    }

    /**
     * @param Package $package
     *
     * @return Package
     */
    private function minimalPackage(Package $package): Package
    {
        return new Package(
            weight: $package->weight,
            length: $package->length,
            width: $package->width,
            height: $package->height,
        );
    }

    /**
     * @param Address|null $address
     *
     * @return Address|null Null when the address has none of the minimal fields, so it is omitted
     */
    private function minimalAddress(?Address $address): ?Address
    {
        if (is_null($address)) {
            return null;
        }

        return $this->nullIfEmpty(new Address(
            city: $address->city,
            postcode: $address->postcode,
            country: $address->country,
        ));
    }

    /**
     * @template T of Arrayable
     *
     * @param T $dto
     *
     * @return T|null
     */
    private function nullIfEmpty(Arrayable $dto): ?Arrayable
    {
        return $dto->toArray() === [] ? null : $dto;
    }
}
