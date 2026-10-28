<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Enums\ServiceType;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class ServiceQuoteResult implements Arrayable, HasSerializableProperties
{
    use SerializesProperties;

    /**
     * @param int|null                             $serviceId
     * @param string|null                          $serviceName
     * @param string|null                          $carrierName
     * @param float|null                           $chargeableWeight
     * @param string|null                          $transitTimeEstimate
     * @param string|null                          $sameDayCollectionCutOffTime
     * @param bool|null                            $isWarehouseService
     * @param TotalCost|null                       $totalCost
     * @param PriceElement[]|null                  $servicePriceBreakdown
     * @param PriceElement[]|null                  $optionalExtras
     * @param bool|null                            $signatureRequiredAvailable
     * @param ExpectedLabel[]|null                 $expectedLabels
     * @param ServiceResultCollectionOption[]|null $collectionOptions
     * @param ServiceType|string|null              $serviceType
     */
    public function __construct(
        readonly public ?int                    $serviceId = null,
        readonly public ?string                 $serviceName = null,
        readonly public ?string                 $carrierName = null,
        readonly public ?float                  $chargeableWeight = null,
        readonly public ?string                 $transitTimeEstimate = null,
        readonly public ?string                 $sameDayCollectionCutOffTime = null,
        readonly public ?bool                   $isWarehouseService = null,
        readonly public ?TotalCost              $totalCost = null,
        readonly public ?array                  $servicePriceBreakdown = null,
        readonly public ?array                  $optionalExtras = null,
        readonly public ?bool                   $signatureRequiredAvailable = null,
        readonly public ?array                  $expectedLabels = null,
        readonly public ?array                  $collectionOptions = null,
        readonly public ServiceType|string|null $serviceType = null,
    )
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'ServiceID' => $this->serviceId,
            'ServiceName' => $this->serviceName,
            'CarrierName' => $this->carrierName,
            'ChargeableWeight' => $this->chargeableWeight,
            'TransitTimeEstimate' => $this->transitTimeEstimate,
            'SameDayCollectionCutOffTime' => $this->sameDayCollectionCutOffTime,
            'IsWarehouseService' => $this->isWarehouseService,
            'TotalCost' => $this->totalCost,
            'ServicePriceBreakdown' => $this->servicePriceBreakdown,
            'OptionalExtras' => $this->optionalExtras,
            'SignatureRequiredAvailable' => $this->signatureRequiredAvailable,
            'ExpectedLabels' => $this->expectedLabels,
            'CollectionOptions' => $this->collectionOptions,
            'ServiceType' => $this->serviceType,
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
            serviceId: $data['ServiceID'] ?? null,
            serviceName: $data['ServiceName'] ?? null,
            carrierName: $data['CarrierName'] ?? null,
            chargeableWeight: $data['ChargeableWeight'] ?? null,
            transitTimeEstimate: $data['TransitTimeEstimate'] ?? null,
            sameDayCollectionCutOffTime: $data['SameDayCollectionCutOffTime'] ?? null,
            isWarehouseService: $data['IsWarehouseService'] ?? null,
            totalCost: isset($data['TotalCost']) ? TotalCost::fromArray($data['TotalCost']) : null,
            servicePriceBreakdown: isset($data['ServicePriceBreakdown'])
                ? array_map(
                    static fn ($price): PriceElement => PriceElement::fromArray($price),
                    $data['ServicePriceBreakdown'],
                )
                : null,
            optionalExtras: isset($data['OptionalExtras'])
                ? array_map(
                    static fn ($option): PriceElement => PriceElement::fromArray($option),
                    $data['OptionalExtras'],
                )
                : null,
            signatureRequiredAvailable: $data['SignatureRequiredAvailable'] ?? null,
            expectedLabels: isset($data['ExpectedLabels'])
                ? array_map(
                    static fn ($label): ExpectedLabel => ExpectedLabel::fromArray($label),
                    $data['ExpectedLabels'],
                )
                : null,
            collectionOptions: isset($data['CollectionOptions'])
                ? array_map(
                    static fn ($option): ServiceResultCollectionOption => ServiceResultCollectionOption::fromArray($option),
                    $data['CollectionOptions'],
                )
                : null,
            serviceType: isset($data['ServiceType'])
                ? (ServiceType::tryFrom($data['ServiceType']) ?: $data['ServiceType'])
                : null,
        );
    }
}
