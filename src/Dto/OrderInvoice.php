<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class OrderInvoice implements Arrayable {
    /**
     * @param string|null             $invoiceReference
     * @param float|null              $totalNet
     * @param float|null              $tax
     * @param float|null              $totalGross
     * @param array<InvoiceItem>|null $invoiceItems
     */
    public function __construct(
        readonly public ?string $invoiceReference = null,
        readonly public ?float $totalNet = null,
        readonly public ?float $tax = null,
        readonly public ?float $totalGross = null,
        readonly public ?array $invoiceItems = null,
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'InvoiceReference' => $this->invoiceReference,
            'TotalNet' => $this->totalNet,
            'Tax' => $this->tax,
            'TotalGross' => $this->totalGross,
            'InvoiceItems' => !is_null($this->invoiceItems)
                ? array_map(static fn ($item): array => $item->toArray(), $this->invoiceItems)
                : null,
        ], static fn ($value): bool => !is_null($value));
    }

    /**
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            invoiceReference: $data['InvoiceReference'] ?? null,
            totalNet: $data['TotalNet'] ?? null,
            tax: $data['Tax'] ?? null,
            totalGross: $data['TotalGross'] ?? null,
            invoiceItems: isset($data['InvoiceItems'])
                ? array_map(
                    fn ($invoiceItem): InvoiceItem => InvoiceItem::fromArray($invoiceItem),
                    $data['InvoiceItems'],
                )
                : null,
        );
    }
}
