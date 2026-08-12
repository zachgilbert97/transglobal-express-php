<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Insurance implements Arrayable {
    /**
     * @param float|null $coverValue
     * @param float|null $excessValue
     * @param bool|null  $goodsAreNew
     * @param bool|null  $goodsAreFragile
     */
    public function __construct(
        readonly public ?float $coverValue = null,
        readonly public ?float $excessValue = null,
        readonly public ?bool $goodsAreNew = null,
        readonly public ?bool $goodsAreFragile = null,
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'coverValue' => $this->coverValue,
            'excessValue' => $this->excessValue,
            'goodsAreNew' => $this->goodsAreNew,
            'goodsAreFragile' => $this->goodsAreFragile,
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
            coverValue: $data['coverValue'] ?? null,
            excessValue: $data['excessValue'] ?? null,
            goodsAreNew: $data['goodsAreNew'] ?? null,
            goodsAreFragile: $data['goodsAreFragile'] ?? null,
        );
    }
}
