<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Enums\Currency;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class ConsignmentCurrency implements Arrayable, HasSerializableProperties
{
    use SerializesProperties;

    /**
     * @param Currency|string|null $currencyCode
     */
    public function __construct(
        readonly public Currency|string|null $currencyCode = null,
    )
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'CurrencyCode' => $this->currencyCode,
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
            currencyCode: isset($data['CurrencyCode'])
                ? (Currency::tryFrom($data['CurrencyCode']) ?: $data['CurrencyCode'])
                : null,
        );
    }
}
