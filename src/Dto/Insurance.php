<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class Insurance implements Arrayable, HasSerializableProperties {
    use SerializesProperties;

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
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'CoverValue' => $this->coverValue,
            'ExcessValue' => $this->excessValue,
            'GoodsAreNew' => $this->goodsAreNew,
            'GoodsAreFragile' => $this->goodsAreFragile,
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
            coverValue: $data['CoverValue'] ?? null,
            excessValue: $data['ExcessValue'] ?? null,
            goodsAreNew: $data['GoodsAreNew'] ?? null,
            goodsAreFragile: $data['GoodsAreFragile'] ?? null,
        );
    }
}
