<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class QuoteSelection implements Arrayable {
    public function __construct(
        readonly public ?int $quoteId = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'QuoteId' => $this->quoteId,
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            quoteId: $data['QuoteId'] ?? null,
        );
    }
}
