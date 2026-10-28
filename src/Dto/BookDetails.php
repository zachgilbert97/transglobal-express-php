<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class BookDetails implements Arrayable, HasSerializableProperties {
    use SerializesProperties;

    /**
     * @param int|null                  $serviceId
     * @param string|null               $yourReference
     * @param string|null               $iossNumber
     * @param bool|null                 $includePreferredOriginNoticeOnPackingList
     * @param float|null                $shippingCharges
     * @param string|null               $deliveryInstructions
     * @param Collection|null           $collection
     * @param array<BookAccessory>|null $bookAccessories
     * @param Insurance|null            $insurance
     */
    public function __construct(
        readonly public ?int $serviceId = null,
        readonly public ?string $yourReference = null,
        readonly public ?string $iossNumber = null,
        readonly public ?bool $includePreferredOriginNoticeOnPackingList = null,
        readonly public ?string $deliveryInstructions = null,
        readonly public ?float $shippingCharges = null,
        readonly public ?Collection $collection = null,
        readonly public ?array $bookAccessories = null,
        readonly public ?Insurance $insurance = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'ServiceID' => $this->serviceId,
            'YourReference' => $this->yourReference,
            'IOSSNumber' => $this->iossNumber,
            'IncludePreferredOriginNoticeOnPackingList' => $this->includePreferredOriginNoticeOnPackingList,
            'ShippingCharges' => $this->shippingCharges,
            'DeliveryInstructions' => $this->deliveryInstructions,
            'Collection' => $this->collection,
            'BookAccessories' => $this->bookAccessories,
            'Insurance' => $this->insurance,
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
            serviceId: $data['ServiceID'] ?? null,
            yourReference: $data['YourReference'] ?? null,
            iossNumber: $data['IOSSNumber'] ?? null,
            includePreferredOriginNoticeOnPackingList: $data['IncludePreferredOriginNoticeOnPackingList'] ?? null,
            deliveryInstructions: $data['DeliveryInstructions'] ?? null,
            shippingCharges: $data['ShippingCharges'] ?? null,
            collection: isset($data['Collection']) ? Collection::fromArray($data['Collection']) : null,
            bookAccessories: isset($data['BookAccessories'])
                ? array_map(
                    static fn ($bookAccessory): BookAccessory => BookAccessory::fromArray($bookAccessory),
                    $data['BookAccessories'],
                )
                : null,
            insurance: isset($data['Insurance']) ? Insurance::fromArray($data['Insurance']) : null,
        );
    }
}
