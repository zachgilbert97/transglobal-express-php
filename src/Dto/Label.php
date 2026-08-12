<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Enums\LabelSize as LabelSizeEnum;
use ZachGilbert\TransglobalExpress\Traits\UnwrapsEnums;

final class Label implements Arrayable {
    use UnwrapsEnums;

    /**
     * @param string|null               $labelRole
     * @param string|null               $labelFormat
     * @param LabelSizeEnum|string|null $labelSize
     * @param string|null               $airWaybillReference
     * @param string|null               $labelContent Base64
     * @param string|null               $downloadUrl
     * @param string|null               $carrierName
     * @param string|null               $serviceName
     */
    public function __construct(
        readonly public ?string $labelRole = null,
        readonly public ?string $labelFormat = null,
        readonly public LabelSizeEnum|string|null $labelSize = null,
        readonly public ?string $airWaybillReference = null,
        readonly public ?string $labelContent = null,
        readonly public ?string $downloadUrl = null,
        readonly public ?string $carrierName = null,
        readonly public ?string $serviceName = null,
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'LabelRole' => $this->labelRole,
            'LabelFormat' => $this->labelFormat,
            'LabelSize' => $this->unwrapEnumValue($this->labelSize),
            'AirWaybillReference' => $this->airWaybillReference,
            'LabelContent' => $this->labelContent,
            'DownloadURL' => !is_null($this->downloadUrl) ? filter_var($this->downloadUrl, FILTER_SANITIZE_URL) : null,
            'CarrierName' => $this->carrierName,
            'ServiceName' => $this->serviceName,
        ], static fn ($value): bool => !is_null($value));
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            labelRole: $data['LabelRole'] ?? null,
            labelFormat: $data['LabelFormat'] ?? null,
            labelSize: isset($data['LabelSize'])
                ? (LabelSizeEnum::tryFrom($data['LabelSize']) ?: $data['LabelSize'])
                : null,
            airWaybillReference: $data['AirWaybillReference'] ?? null,
            labelContent: $data['LabelContent'] ?? null,
            downloadUrl: $data['DownloadURL'] ?? null,
            carrierName: $data['CarrierName'] ?? null,
            serviceName: $data['ServiceName'] ?? null,
        );
    }
}
