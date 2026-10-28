<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class ServiceResultCollectionOption implements Arrayable, HasSerializableProperties
{
    use SerializesProperties;

    /**
     * @param int|null           $collectionOptionId
     * @param string|null        $collectionOptionTitle
     * @param float|null         $collectionCharge
     * @param string|null        $sameDayCollectionCutOffTime
     * @param ExpectedLabel|null $expectedLabel
     */
    public function __construct(
        readonly public ?int $collectionOptionId = null,
        readonly public ?string $collectionOptionTitle = null,
        readonly public ?float $collectionCharge = null,
        readonly public ?string $sameDayCollectionCutOffTime = null,
        readonly public ?ExpectedLabel $expectedLabel = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'CollectionOptionID' => $this->collectionOptionId,
            'CollectionOptionTitle' => $this->collectionOptionTitle,
            'CollectionCharge' => $this->collectionCharge,
            'SameDayCollectionCutOffTime' => $this->sameDayCollectionCutOffTime,
            'ExpectedLabel' => $this->expectedLabel,
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
            collectionOptionId: $data['CollectionOptionID'] ?? null,
            collectionOptionTitle: $data['CollectionOptionTitle'] ?? null,
            collectionCharge: $data['CollectionCharge'] ?? null,
            sameDayCollectionCutOffTime: $data['SameDayCollectionCutOffTime'] ?? null,
            expectedLabel: isset($data['ExpectedLabel'])
                ? ExpectedLabel::fromArray($data['ExpectedLabel'])
                : null,
        );
    }
}
