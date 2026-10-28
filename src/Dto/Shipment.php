<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class Shipment implements Arrayable, HasSerializableProperties
{
    use SerializesProperties;

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
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'Consignment' => $this->consignment,
            'CollectionAddress' => $this->collectionAddress,
            'DeliveryAddress' => $this->deliveryAddress,
            'ExporterAddress' => $this->exporterAddress,
            'ImporterAddress' => $this->importerAddress,
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
            consignment: isset($data['Consignment']) ? Consignment::fromArray($data['Consignment']) : null,
            collectionAddress: isset($data['CollectionAddress']) ? Address::fromArray($data['CollectionAddress']) : null,
            deliveryAddress: isset($data['DeliveryAddress']) ? Address::fromArray($data['DeliveryAddress']) : null,
            exporterAddress: isset($data['ExporterAddress']) ? Address::fromArray($data['ExporterAddress']) : null,
            importerAddress: isset($data['ImporterAddress']) ? Address::fromArray($data['ImporterAddress']) : null,
        );
    }
}
