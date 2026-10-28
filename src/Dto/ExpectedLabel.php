<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Enums\LabelGenerateStatus;
use ZachGilbert\TransglobalExpress\Enums\LabelRole;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class ExpectedLabel implements Arrayable, HasSerializableProperties
{
    use SerializesProperties;

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
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'LabelRole' => $this->labelRole,
            'LabelFormat' => $this->labelFormat,
            'LabelGenerateStatus' => $this->labelGenerateStatus,
            'AvailableSizes' => $this->availableSizes,
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
            labelRole: isset($data['LabelRole'])
                ? (LabelRole::tryFrom($data['LabelRole']) ?: $data['LabelRole'])
                : null,
            labelFormat: $data['LabelFormat'] ?? null,
            labelGenerateStatus: isset($data['LabelGenerateStatus'])
                ? (LabelGenerateStatus::tryFrom($data['LabelGenerateStatus']) ?: $data['LabelGenerateStatus'])
                : null,
            availableSizes: isset($data['AvailableSizes'])
                ? array_map(static fn ($size): LabelSize => LabelSize::fromArray($size), $data['AvailableSizes'])
                : null,
        );
    }
}
