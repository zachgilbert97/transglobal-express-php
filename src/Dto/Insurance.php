<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Insurance implements Arrayable {
    public function __construct(
        readonly public ?float $coverValue = null,
        readonly public ?float $excessValue = null,
        readonly public ?bool $goodsAreNew = null,
        readonly public ?bool $goodsAreFragile = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'coverValue' => $this->coverValue,
            'excessValue' => $this->excessValue,
            'goodsAreNew' => $this->goodsAreNew,
            'goodsAreFragile' => $this->goodsAreFragile,
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            coverValue: $data['coverValue'] ?? null,
            excessValue: $data['excessValue'] ?? null,
            goodsAreNew: $data['goodsAreNew'] ?? null,
            goodsAreFragile: $data['goodsAreFragile'] ?? null,
        );
    }
}
