<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Shipment implements Arrayable
{
    /**
     * @param Consignment|null $consignment
     * @param Address|null     $collectionAddress
     * @param Address|null     $deliveryAddress
     * @param Address|null     $exporterAddress
     * @param Address|null     $importerAddress
     */
    public function __construct(
        readonly public ?Consignment $consignment = null,
        readonly public ?Address     $collectionAddress = null,
        readonly public ?Address     $deliveryAddress = null,
        readonly public ?Address     $exporterAddress = null,
        readonly public ?Address     $importerAddress = null,
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'Consignment' => $this->consignment?->toArray(),
            'CollectionAddress' => $this->collectionAddress?->toArray(),
            'DeliveryAddress' => $this->deliveryAddress?->toArray(),
            'ExporterAddress' => $this->exporterAddress?->toArray(),
            'ImporterAddress' => $this->importerAddress?->toArray(),
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
            consignment: isset($data['Consignment']) ? Consignment::fromArray($data['Consignment']) : null,
            collectionAddress: isset($data['CollectionAddress']) ? Address::fromArray($data['CollectionAddress']) : null,
            deliveryAddress: isset($data['DeliveryAddress']) ? Address::fromArray($data['DeliveryAddress']) : null,
            exporterAddress: isset($data['ExporterAddress']) ? Address::fromArray($data['ExporterAddress']) : null,
            importerAddress: isset($data['ImporterAddress']) ? Address::fromArray($data['ImporterAddress']) : null,
        );
    }
}
