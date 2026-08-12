<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class QuoteSelection implements Arrayable {
    /**
     * @param int|null $quoteId
     */
    public function __construct(
        readonly public ?int $quoteId = null,
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'QuoteId' => $this->quoteId,
        ], static fn ($value): bool => !is_null($value));
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            quoteId: $data['QuoteId'] ?? null,
        );
    }
}
