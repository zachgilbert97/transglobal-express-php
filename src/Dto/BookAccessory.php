<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class BookAccessory implements Arrayable, HasSerializableProperties {
    use SerializesProperties;

    /**
     * @param string|null $code
     */
    public function __construct(
        readonly public ?string $code = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'Code' => $this->code,
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
            code: $data['Code'] ?? null,
        );
    }
}
