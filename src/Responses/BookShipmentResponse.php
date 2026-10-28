<?php

namespace ZachGilbert\TransglobalExpress\Responses;

use ZachGilbert\TransglobalExpress\Concerns\Response;
use ZachGilbert\TransglobalExpress\Dto\Document;
use ZachGilbert\TransglobalExpress\Dto\Label;
use ZachGilbert\TransglobalExpress\Dto\Notification;
use ZachGilbert\TransglobalExpress\Dto\OrderInvoice;
use ZachGilbert\TransglobalExpress\Enums\ResponseStatus;

final readonly class BookShipmentResponse implements Response
{
    public function __construct(
        public ResponseStatus|string|null $status = null,
        public ?array                     $notifications = null,
        public ?string                    $orderReference = null,
        public ?OrderInvoice              $orderInvoice = null,
        public ?array                     $labels = null,
        public ?array                     $documents = null,
        public ?string                    $trackingUrl = null,
        public ?string                    $failReference = null,
    ) {}

    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            status: isset($data['Status'])
                ? (ResponseStatus::tryFrom($data['Status']) ?: $data['Status'])
                : null,
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
