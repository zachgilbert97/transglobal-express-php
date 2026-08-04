<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class InvoiceItem implements Arrayable {
    public function __construct(
        readonly public ?string $itemDescription = null,
        readonly public ?float $amountNet = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'ItemDescription' => $this->itemDescription,
            'AmountNet' => $this->amountNet,
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            itemDescription: $data['ItemDescription'] ?? null,
            amountNet: $data['AmountNet'] ?? null,
        );
    }
}
