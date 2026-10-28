<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class InvoiceItem implements Arrayable, HasSerializableProperties {
    use SerializesProperties;

    /**
     * @param string|null $itemDescription
     * @param float|null  $amountNet
     */
    public function __construct(
        readonly public ?string $itemDescription = null,
        readonly public ?float $amountNet = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'ItemDescription' => $this->itemDescription,
            'AmountNet' => $this->amountNet,
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
            itemDescription: $data['ItemDescription'] ?? null,
            amountNet: $data['AmountNet'] ?? null,
        );
    }
}
