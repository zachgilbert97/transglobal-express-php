<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Enums\LabelSize as LabelSizeEnum;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class LabelSize implements Arrayable, HasSerializableProperties
{
    use SerializesProperties;

    /**
     * @param LabelSizeEnum|string|null $size
     */
    public function __construct(
        public readonly LabelSizeEnum|string|null $size = null,
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'Size' => $this->size,
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
            size: isset($data['Size'])
                ? (LabelSizeEnum::tryFrom($data['Size']) ?: $data['Size'])
                : null,
        );
    }
}
