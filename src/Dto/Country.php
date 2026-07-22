<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Country implements Arrayable {
    public function __construct(
        readonly public ?int $countryId = null,
        readonly public ?string $countryCode = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'CountryId' => $this->countryId,
            'CountryCode' => $this->countryCode,
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            countryId: $data['CountryId'] ?? null,
            countryCode: $data['CountryCode'] ?? null,
        );
    }
}
