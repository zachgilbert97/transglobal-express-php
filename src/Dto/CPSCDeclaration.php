<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Enums\CPSCDisclaimerType;
use ZachGilbert\TransglobalExpress\Enums\DeclarationOption;
use ZachGilbert\TransglobalExpress\Traits\UnwrapsEnums;

final class CPSCDeclaration implements Arrayable {
    use UnwrapsEnums;

    public function __construct(
        readonly public DeclarationOption|int|null $declarationOption = null,
        readonly public ?string $productId = null,
        readonly public ?string $cpscRuleCitation = null,
        readonly public ?string $dateOfManufacture = null,
        readonly public ?string $placeOfManufacture = null,
        readonly public ?string $dateOfMostRecentTest = null,
        readonly public ?string $testingLaboratory = null,
        readonly public ?string $recordsKeeperContact = null,
        readonly public ?string $certifierId = null,
        readonly public ?string $versionId = null,
        readonly public CPSCDisclaimerType|string|null $disclaimerType = null,
        readonly public ?string $intendedUseCode = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'DeclarationOption' => $this->unwrapEnumValue($this->declarationOption),
            'ProductId' => $this->productId,
            'CPSCRuleCitation' => $this->cpscRuleCitation,
            'DateOfManufacture' => $this->dateOfManufacture,
            'PlaceOfManufacture' => $this->placeOfManufacture,
            'DateOfMostRecentTest' => $this->dateOfMostRecentTest,
            'TestingLaboratory' => $this->testingLaboratory,
            'RecordsKeeperContact' => $this->recordsKeeperContact,
            'CertifierId' => $this->certifierId,
            'VersionId' => $this->versionId,
            'DisclaimerType' => $this->unwrapEnumValue($this->disclaimerType),
            'IntendedUseCode' => $this->intendedUseCode,
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
            declarationOption: isset($data['DeclarationOption']) ? DeclarationOption::from($data['DeclarationOption']) : null,
            productId: $data['ProductId'] ?? null,
            cpscRuleCitation: $data['CPSCRuleCitation'] ?? null,
            dateOfManufacture: $data['DateOfManufacture'] ?? null,
            placeOfManufacture: $data['PlaceOfManufacture'] ?? null,
            dateOfMostRecentTest: $data['DateOfMostRecentTest'] ?? null,
            testingLaboratory: $data['TestingLaboratory'] ?? null,
            recordsKeeperContact: $data['RecordsKeeperContact'] ?? null,
            certifierId: $data['CertifierId'] ?? null,
            versionId: $data['VersionId'] ?? null,
            disclaimerType: isset($data['DisclaimerType']) ? CPSCDisclaimerType::from($data['DisclaimerType']) : null,
            intendedUseCode: $data['IntendedUseCode'] ?? null,
        );
    }
}
