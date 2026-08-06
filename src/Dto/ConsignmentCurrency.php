<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Enums\Currency;
use ZachGilbert\TransglobalExpress\Traits\UnwrapsEnums;

final class ConsignmentCurrency implements Arrayable
{
    use UnwrapsEnums;

    /**
     * @param Currency|string|null $currencyCode
     */
    public function __construct(
        readonly public Currency|string|null $currencyCode = null,
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'CurrencyCode' => $this->unwrapEnumValue($this->currencyCode),
        ], static fn($value): bool => $value !== null);
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
