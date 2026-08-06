<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Enums\LabelSize as LabelSizeEnum;
use ZachGilbert\TransglobalExpress\Traits\UnwrapsEnums;

final class LabelSize implements Arrayable
{
    use UnwrapsEnums;

    /**
     * @param LabelSizeEnum|string|null $size
     */
    public function __construct(
        public readonly LabelSizeEnum|string|null $size = null,
    ) {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'Size' => $this->unwrapEnumValue($this->size),
        ], static fn ($value): bool => $value !== null);
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            size: isset($data['Size'])
                ? (LabelSizeEnum::tryFrom($data['Size']) ?: $data['Size'])
                : null,
        );
    }
}
