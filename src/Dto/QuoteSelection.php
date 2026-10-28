<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class QuoteSelection implements Arrayable, HasSerializableProperties {
    use SerializesProperties;

    /**
     * @param int|null $quoteId
     */
    public function __construct(
        readonly public ?int $quoteId = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'QuoteID' => $this->quoteId,
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
            quoteId: $data['QuoteID'] ?? null,
        );
    }
}
