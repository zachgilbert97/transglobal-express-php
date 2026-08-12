<?php

namespace ZachGilbert\TransglobalExpress\Responses;

use ZachGilbert\TransglobalExpress\Concerns\Response;
use ZachGilbert\TransglobalExpress\Dto\Notification;
use ZachGilbert\TransglobalExpress\Dto\ServiceQuoteResult;
use ZachGilbert\TransglobalExpress\Enums\ResponseStatus;

final class GetQuoteResponse implements Response
{
    /**
     * @param ResponseStatus|string|null     $status
     * @param array<Notification>|null       $notifications
     * @param int|null                       $quoteId
     * @param array<ServiceQuoteResult>|null $serviceResults
     */
    public function __construct(
        public readonly ResponseStatus|string|null $status = null,
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
            status: isset($data['Status'])
                ? (ResponseStatus::tryFrom($data['Status']) ?? $data['Status'])
                : null,
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
