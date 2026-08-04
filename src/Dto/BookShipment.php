<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class BookShipment implements Arrayable
{
    /**
     * @param string|null          $orderReference
     * @param OrderInvoice|null    $orderInvoice
     * @param array<Label>|null    $labels
     * @param array<Document>|null $documents
     * @param string|null          $trackingUrl
     */
    public function __construct(
        public readonly ?string $orderReference = null,
        public readonly ?OrderInvoice $orderInvoice = null,
        public readonly ?array $labels = null,
        public readonly ?array $documents = null,
        public readonly ?string $trackingUrl = null,
    ) {
    }

    public function toArray(): array
    {
        return array_filter([
            'OrderReference' => $this->orderReference,
            'OrderInvoice' => $this->orderInvoice,
            'Labels' => $this->labels,
            'Documents' => $this->documents,
            'TrackingURL' => $this->trackingUrl,
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            orderReference: $data['OrderReference'] ?? null,
            orderInvoice: isset($data['OrderInvoice'])
                ? OrderInvoice::fromArray($data['OrderInvoice'])
                : null,
            labels: isset($data['Labels'])
                ? array_map(fn ($label): Label => Label::fromArray($label), $data['Labels'])
                : null,
            documents: isset($data['Documents'])
                ? array_map(fn ($document): Document => Document::fromArray($document), $data['Documents'])
                : null,
            trackingUrl: $data['TrackingURL'] ?? null,
        );
    }
}
