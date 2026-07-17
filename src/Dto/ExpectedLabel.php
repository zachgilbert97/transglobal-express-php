<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Enums\LabelGenerateStatus;
use ZachGilbert\TransglobalExpress\Enums\LabelRole;
use ZachGilbert\TransglobalExpress\Traits\UnwrapsEnums;

final class ExpectedLabel implements Arrayable
{
    use UnwrapsEnums;

    /**
     * @param LabelRole|string|null           $labelRole
     * @param string|null                     $labelFormat
     * @param LabelGenerateStatus|string|null $labelGenerateStatus
     * @param LabelSize[]|null                $availableSizes
     */
    public function __construct(
        readonly public LabelRole|string|null           $labelRole = null,
        readonly public ?string                         $labelFormat = null,
        readonly public LabelGenerateStatus|string|null $labelGenerateStatus = null,
        readonly public ?array                          $availableSizes = null,
    )
    {
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'LabelRole' => $this->unwrapEnumValue($this->labelRole),
            'LabelFormat' => $this->labelFormat,
            'LabelGenerateStatus' => $this->unwrapEnumValue($this->labelGenerateStatus),
            'AvailableSizes' => $this->availableSizes,
        ], static fn($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            labelRole: isset($data['LabelRole']) ? LabelRole::from($data['LabelRole']) : null,
            labelFormat: $data['LabelFormat'] ?? null,
            labelGenerateStatus: isset($data['LabelGenerateStatus']) ? LabelGenerateStatus::from($data['LabelGenerateStatus']) : null,
            availableSizes: $data['AvailableSizes'] ?? null,
        );
    }
}