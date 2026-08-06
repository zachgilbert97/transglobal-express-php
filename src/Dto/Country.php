<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Country implements Arrayable {
    /**
     * @param int|null    $countryId
     * @param string|null $title
     * @param string|null $countryCode
     */
    public function __construct(
        readonly public ?int $countryId = null,
        readonly public ?string $title = null,
        readonly public ?string $countryCode = null,
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'CountryId' => $this->countryId,
            'Title' => $this->title,
            'CountryCode' => $this->countryCode,
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
            countryId: $data['CountryId'] ?? null,
            title: $data['Title'] ?? null,
            countryCode: $data['CountryCode'] ?? null,
        );
    }
}
