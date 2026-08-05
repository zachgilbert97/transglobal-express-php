<?php

namespace ZachGilbert\TransglobalExpress\Responses;

use ZachGilbert\TransglobalExpress\Concerns\Response;
use ZachGilbert\TransglobalExpress\Dto\Document;
use ZachGilbert\TransglobalExpress\Dto\Label;
use ZachGilbert\TransglobalExpress\Dto\Notification;
use ZachGilbert\TransglobalExpress\Dto\OrderInvoice;

final class BookShipmentResponse implements Response
{
    public function __construct(
        public readonly ?string $status = null, // TODO - Enum
        public readonly ?array $notifications = null,
        public readonly ?string $orderReference = null,
        public readonly ?OrderInvoice $orderInvoice = null,
        public readonly ?array $labels = null,
        public readonly ?array $documents = null,
        public readonly ?string $trackingUrl = null,
        public readonly ?string $failReference = null,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            status: $data['Status'] ?? null,
            notifications: isset($data['Notifications'])
                ? array_map(
                    fn ($notification): Notification => Notification::fromArray($notification),
                    $data['Notifications'],
                )
                : null,
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
            failReference: $data['FailReference'] ?? null,
        );
    }
}
