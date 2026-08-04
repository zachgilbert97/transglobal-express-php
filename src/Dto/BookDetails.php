<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class BookDetails implements Arrayable {
    /**
     * @param int|null                  $serviceId
     * @param string|null               $yourReference
     * @param string|null               $iossNumber
     * @param bool|null                 $includePreferredOriginNoticeOnPackingList
//     * @param Collection|null           $collection
//     * @param array<BookAccessory>|null $bookAccessories
//     * @param Insurance|null            $insurance
     * @param float|null                $shippingCharges
     * @param string|null               $deliveryInstructions
     */
    public function __construct(
        readonly public ?int $serviceId = null,
        readonly public ?string $yourReference = null,
        readonly public ?string $iossNumber = null,
        readonly public ?bool $includePreferredOriginNoticeOnPackingList = null,
//        readonly public ?Collection $collection = null,
//        readonly public ?array $bookAccessories = null,
//        readonly public ?Insurance $insurance = null,
        readonly public ?float $shippingCharges = null,
        readonly public ?string $deliveryInstructions = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'ServiceId' => $this->serviceId,
            'YourReference' => $this->yourReference,
            'IOSSNumber' => $this->iossNumber,
            'IncludePreferredOriginNoticeOnPackingList' => $this->includePreferredOriginNoticeOnPackingList,
//            'Collection' => $this->collection?->toArray(),
//            'BookAccessories' => $this->bookAccessories,
//            'Insurance' => $this->insurance?->toArray(),
            'ShippingCharges' => $this->shippingCharges,
            'DeliveryInstructions' => $this->deliveryInstructions,
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            serviceId: $data['ServiceId'] ?? null,
            yourReference: $data['YourReference'] ?? null,
            shippingCharges: $data['ShippingCharges'] ?? null,
            collection: isset($data['Collection']) ? Collection::fromArray($data['Collection']) : null,
            bookAccessories: isset($data['BookAccessories'])
                ? array_map(
                    fn ($bookAccessory): BookAccessory => BookAccessory:: fromArray($bookAccessory),
                    $data['BookAccessories'],
                )
                : null,
            insurance: isset($data['Insurance']) ? Insurance::fromArray($data['Insurance']) : null,
            deliveryInstructions: $data['DeliveryInstructions'] ?? null,
        );
    }
}
