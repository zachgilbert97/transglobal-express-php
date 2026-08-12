<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Package implements Arrayable
{
    /**
     * @param float|null           $weight
     * @param float|null           $length
     * @param float|null           $width
     * @param float|null           $height
     * @param CommodityLine[]|null $commodityDetails
     */
    public function __construct(
        readonly public ?float $weight = null,
        readonly public ?float $length = null,
        readonly public ?float $width = null,
        readonly public ?float $height = null,
        readonly public ?array $commodityDetails = null,
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'Weight' => $this->weight,
            'Length' => $this->length,
            'Width' => $this->width,
            'Height' => $this->height,
            'CommodityDetails' => !is_null($this->commodityDetails)
                ? array_map(static fn ($detail): array => $detail->toArray(), $this->commodityDetails)
                : null,
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
            weight: $data['Weight'] ?? null,
            length: $data['Length'] ?? null,
            width: $data['Width'] ?? null,
            height: $data['Height'] ?? null,
            commodityDetails: isset($data['CommodityDetails'])
                ? array_map(
                    static fn ($commodityLine): CommodityLine => CommodityLine::fromArray($commodityLine),
                    $data['CommodityDetails'],
                )
                : null,
        );
    }
}
