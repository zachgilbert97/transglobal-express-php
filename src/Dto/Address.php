<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Address implements Arrayable {
    public function __construct(
        readonly public ?string $forename = null,
        readonly public ?string $surname = null,
        readonly public ?string $emailAddress = null,
        readonly public ?string $telephoneNumber = null,
        readonly public ?string $mobileNumber = null,
        readonly public ?string $companyName = null,
        readonly public ?string $addressLineOne = null,
        readonly public ?string $addressLineTwo = null,
        readonly public ?string $city = null,
        readonly public ?string $county = null,
        readonly public ?string $postcode = null,
        readonly public ?Country $country = null,
        readonly public ?bool $isAddressResidential = null,
        readonly public ?bool $eoriNumber = null,
        readonly public ?string $vatNumber = null,
    ) {}

    public function toArray(): array
    {
        return array_filter([
            'Forename' => $this->forename,
            'Surname' => $this->surname,
            'EmailAddress' => $this->emailAddress,
            'TelephoneNumber' => $this->telephoneNumber,
            'MobileNumber' => $this->mobileNumber,
            'CompanyName' => $this->companyName,
            'AddressLineOne' => $this->addressLineOne,
            'AddressLineTwo' => $this->addressLineTwo,
            'City' => $this->city,
            'County' => $this->county,
            'Postcode' => $this->postcode,
            'Country' => $this->country?->toArray(),
            'IsAddressResidential' => $this->isAddressResidential,
            'EORINumber' => $this->eoriNumber,
            'VATNumber' => $this->vatNumber,
        ], static fn ($value): bool => $value !== null);
    }

    public static function fromArray(array $data): static
    {
        return new self(
             forename: $data['Forename'] ?? null,
             surname: $data['Surname'] ?? null,
             emailAddress: $data['EmailAddress'] ?? null,
             telephoneNumber: $data['TelephoneNumber'] ?? null,
             mobileNumber: $data['MobileNumber'] ?? null,
             companyName: $data['CompanyName'] ?? null,
             addressLineOne: $data['AddressLineOne'] ?? null,
             addressLineTwo: $data['AddressLineTwo'] ?? null,
             city: $data['City'] ?? null,
             county: $data['County'] ?? null,
             postcode: $data['Postcode'] ?? null,
             country: isset($data['Country']) ? Country::fromArray($data['Country']) : null,
             isAddressResidential: $data['IsAddressResidential'] ?? null,
             eoriNumber: $data['EORINumber'] ?? null,
             vatNumber: $data['VATNumber'] ?? null,
        );
    }
}
