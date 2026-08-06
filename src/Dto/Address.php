<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Address implements Arrayable {
    /**
     * @param string|null  $forename
     * @param string|null  $surname
     * @param string|null  $emailAddress
     * @param string|null  $telephoneNumber
     * @param string|null  $mobileNumber
     * @param string|null  $companyName
     * @param string|null  $addressLineOne
     * @param string|null  $addressLineTwo
     * @param string|null  $city
     * @param string|null  $county
     * @param string|null  $postcode
     * @param Country|null $country
     * @param bool|null    $isAddressResidential
     * @param bool|null    $eoriNumber
     * @param string|null  $vatNumber
     */
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

    /**
     * @return array
     */
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

    /**
     * @param array $data
     *
     * @return static
     */
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
