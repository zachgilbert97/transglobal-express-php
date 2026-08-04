<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Collection implements Arrayable {
    /**
     * @param string|null $collectionDate Y-m-d, e.g. 2020-12-31
     * @param string|null $readyFrom H:m, e.g. 12:30
     * @param int|null    $collectionOptionId
     */
    public function __construct(
        readonly public ?string $collectionDate = null,
        readonly public ?string $readyFrom = null,
        readonly public ?int $collectionOptionId = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'CollectionDate' => $this->collectionDate,
            'ReadyFrom' => $this->readyFrom,
            'CollectionOptionID' => $this->collectionOptionId,
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            collectionDate: $data['CollectionDate'] ?? null,
            readyFrom: $data['ReadyFrom'] ?? null,
            collectionOptionId: $data['CollectionOptionID'] ?? null,
        );
    }
}
