<?php

namespace ZachGilbert\TransglobalExpress\Responses;

use ZachGilbert\TransglobalExpress\Concerns\Response;
use ZachGilbert\TransglobalExpress\Dto\Notification;
use ZachGilbert\TransglobalExpress\Dto\ServiceQuoteResult;

final class GetQuoteResponse implements Response
{
    /**
     * @param string|null                    $status
     * @param array<Notification>|null       $notifications
     * @param int|null                       $quoteId
     * @param array<ServiceQuoteResult>|null $serviceResults
     */
    public function __construct(
        public readonly ?string $status = null, // TODO - Enum
        public readonly ?array $notifications = null,
        public readonly ?int $quoteId = null,
        public readonly ?array $serviceResults = null,
    ) {}

    /**
     * @param array $data
     *
     * @return static
     */
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
            quoteId: $data['QuoteID'] ?? null,
            serviceResults: isset($data['ServiceResults'])
                ? array_map(
                    fn ($serviceResult): ServiceQuoteResult => ServiceQuoteResult::fromArray($serviceResult),
                    $data['ServiceResults'],
                )
                : null,
        );
    }
}
