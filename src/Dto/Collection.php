<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class Collection implements Arrayable, HasSerializableProperties {
    use SerializesProperties;

    /**
     * @param string|null $collectionDate The collection date. Y-m-d, e.g. 2020-12-31
     * @param string|null $readyFrom The time from which the consignment will be ready or collection. H:m, e.g. 12:30
     * @param int|null    $collectionOptionId
     */
    public function __construct(
        readonly public ?string $collectionDate = null,
        readonly public ?string $readyFrom = null,
        readonly public ?int $collectionOptionId = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'CollectionDate' => $this->collectionDate,
            'ReadyFrom' => $this->readyFrom,
            'CollectionOptionID' => $this->collectionOptionId,
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
            collectionDate: $data['CollectionDate'] ?? null,
            readyFrom: $data['ReadyFrom'] ?? null,
            collectionOptionId: $data['CollectionOptionID'] ?? null,
        );
    }
}
