<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Quote implements Arrayable
{
    /**
     * @param int|null                  $quoteId
     * @param ServiceQuoteResult[]|null $serviceResults
     */
    public function __construct(
        readonly public ?int   $quoteId = null,
        readonly public ?array $serviceResults = null,
    )
    {
    }

    public function toArray(): array
    {
        return array_filter([
            'QuoteId' => $this->quoteId,
            'ServiceResults' => $this->serviceResults,
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
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