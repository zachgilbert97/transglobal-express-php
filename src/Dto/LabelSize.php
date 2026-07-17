<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Traits\UnwrapsEnums;
use ZachGilbert\TransglobalExpress\Enums\LabelSize as LabelSizeEnum;

final class LabelSize implements Arrayable
{
    use UnwrapsEnums;

    public function __construct(
        readonly public LabelSizeEnum|string|null $size = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'Size' => $this->unwrapEnumValue($this->size),
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            size: isset($data['Size']) ? LabelSizeEnum::from($data['Size']) : null,
        );
    }
}