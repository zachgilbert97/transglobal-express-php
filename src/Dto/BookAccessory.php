<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class BookAccessory implements Arrayable {
    /**
     * @param string|null $code
     */
    public function __construct(
        readonly public ?string $code = null,
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'Code' => $this->code,
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
            code: $data['Code'] ?? null,
        );
    }
}
