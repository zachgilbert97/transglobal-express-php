<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class Country implements Arrayable, HasSerializableProperties {
    use SerializesProperties;

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
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'CountryID' => $this->countryId,
            'Title' => $this->title,
            'CountryCode' => $this->countryCode,
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
            countryId: $data['CountryID'] ?? null,
            title: $data['Title'] ?? null,
            countryCode: $data['CountryCode'] ?? null,
        );
    }
}
