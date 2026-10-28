<?php

namespace ZachGilbert\TransglobalExpress\Tests;

/**
 * Fully populated API payloads.
 *
 * Keys are listed in the order each DTO's toArray() emits them, so a payload can be
 * compared strictly against fromArray($payload)->toArray().
 */
final class Fixtures
{
    public static function country(array $overrides = []): array
    {
        return array_replace([
            'CountryID' => 235,
            'Title' => 'United Kingdom',
            'CountryCode' => 'GB',
        ], $overrides);
    }

    public static function address(array $overrides = []): array
    {
        return array_replace([
            'Forename' => 'Jane',
            'Surname' => 'Doe',
            'EmailAddress' => 'jane@example.com',
            'TelephoneNumber' => '02079460000',
            'MobileNumber' => '07700900000',
            'CompanyName' => 'Example Ltd',
            'AddressLineOne' => '1 High Street',
            'AddressLineTwo' => 'Suite 2',
            'City' => 'London',
            'County' => 'Greater London',
            'Postcode' => 'SW1A 1AA',
            'Country' => self::country(),
            'IsAddressResidential' => false,
            'EORINumber' => 'GB123456789000',
            'VATNumber' => 'GB123456789',
        ], $overrides);
    }

    public static function cpscDeclaration(): array
    {
        return [
            'DeclarationOption' => 2,
            'ProductID' => 'PROD-001',
            'CPSCRuleCitation' => '16 CFR 1500',
            'DateOfManufacture' => '2026-01-15',
            'PlaceOfManufacture' => 'Manchester',
            'DateOfMostRecentTest' => '2026-02-01',
            'TestingLaboratory' => 'Example Labs',
            'RecordsKeeperContact' => 'records@example.com',
            'CertifierID' => 'CERT-1',
            'VersionID' => 'V1',
            'DisclaimerType' => 'A',
            'IntendedUseCode' => 'GEN',
        ];
    }

    public static function commodityLine(): array
    {
        return [
            'CommodityCode' => '6109100010',
            'CommodityDescription' => 'Cotton T-shirts',
            'CountryOfOrigin' => self::country(),
            'NumberOfUnits' => 10,
            'UnitValue' => 12.5,
            'UnitWeight' => 0.2,
            'ProductCode' => 'TSHIRT-M',
            'ManufacturerAddress' => self::address(),
            'CpscDeclaration' => self::cpscDeclaration(),
            'ProductIdentifiers' => self::productIdentifiers(),
        ];
    }

    public static function productIdentifiers(): array
    {
        return [
            'MerchantProductIdentifier' => 'TSHIRT-M-BLUE',
            'NonStandardisedProductIdentifier' => 'SKU-48213',
            'StandardisedProductIdentifier' => '5012345678900',
            'NoStandardisedIdentifierAvailable' => false,
        ];
    }

    public static function package(): array
    {
        return [
            'Weight' => 2.5,
            'Length' => 30.0,
            'Width' => 20.0,
            'Height' => 10.0,
            'CommodityDetails' => [self::commodityLine()],
        ];
    }

    public static function consignmentCurrency(): array
    {
        return ['CurrencyCode' => 'GBP'];
    }

    public static function consignment(): array
    {
        return [
            'ItemType' => 'Parcel',
            'ItemsAreStackable' => false,
            'ConsignmentSummary' => 'Clothing',
            'ConsignmentValue' => 125.0,
            'ConsignmentCurrency' => self::consignmentCurrency(),
            'Packages' => [self::package()],
        ];
    }

    public static function deliveryAddress(): array
    {
        return self::address([
            'City' => 'Paris',
            'County' => 'Île-de-France',
            'Postcode' => '75001',
            'Country' => self::country(['CountryID' => 74, 'Title' => 'France', 'CountryCode' => 'FR']),
        ]);
    }

    public static function shipment(): array
    {
        return [
            'Consignment' => self::consignment(),
            'CollectionAddress' => self::address(),
            'DeliveryAddress' => self::deliveryAddress(),
            'ExporterAddress' => self::address(),
            'ImporterAddress' => self::deliveryAddress(),
        ];
    }

    public static function collection(): array
    {
        return [
            'CollectionDate' => '2026-11-02',
            'ReadyFrom' => '09:30',
            'CollectionOptionID' => 3,
        ];
    }

    public static function bookAccessory(): array
    {
        return ['Code' => 'SIGNATURE'];
    }

    public static function insurance(): array
    {
        return [
            'CoverValue' => 500.0,
            'ExcessValue' => 50.0,
            'GoodsAreNew' => true,
            'GoodsAreFragile' => false,
        ];
    }

    public static function bookDetails(): array
    {
        return [
            'ServiceID' => 1234,
            'YourReference' => 'ORDER-1001',
            'IOSSNumber' => 'IM1234567890',
            'IncludePreferredOriginNoticeOnPackingList' => true,
            'ShippingCharges' => 9.99,
            'DeliveryInstructions' => 'Leave with reception',
            'Collection' => self::collection(),
            'BookAccessories' => [self::bookAccessory()],
            'Insurance' => self::insurance(),
        ];
    }

    public static function notification(): array
    {
        return ['Message' => 'Quote generated', 'Severity' => 'INFO'];
    }

    public static function invoiceItem(): array
    {
        return ['ItemDescription' => 'Shipping', 'AmountNet' => 20.0];
    }

    public static function orderInvoice(): array
    {
        return [
            'InvoiceReference' => 'INV-5001',
            'TotalNet' => 20.0,
            'Tax' => 4.0,
            'TotalGross' => 24.0,
            'InvoiceItems' => [self::invoiceItem()],
        ];
    }

    public static function document(): array
    {
        return [
            'DocumentType' => 'CommercialInvoice',
            'Format' => 'PDF',
            'Content' => base64_encode('%PDF-document'),
            'DownloadURL' => 'https://example.com/documents/invoice.pdf',
        ];
    }

    public static function label(): array
    {
        return [
            'LabelRole' => 'Primary',
            'LabelFormat' => 'PDF',
            'LabelSize' => 'Thermal',
            'AirWaybillReference' => 'AWB123456',
            'LabelContent' => base64_encode('%PDF-label'),
            'DownloadURL' => 'https://example.com/labels/label.pdf',
            'CarrierName' => 'Example Carrier',
            'ServiceName' => 'Express Saver',
        ];
    }

    public static function labelSize(): array
    {
        return ['Size' => 'A4'];
    }

    public static function expectedLabel(): array
    {
        return [
            'LabelRole' => 'Primary',
            'LabelFormat' => 'PDF',
            'LabelGenerateStatus' => 'LabelGenerated',
            'AvailableSizes' => [['Size' => 'A4'], ['Size' => 'Thermal']],
        ];
    }

    public static function priceElement(): array
    {
        return ['Code' => 'FUEL', 'Description' => 'Fuel surcharge', 'Cost' => 1.75];
    }

    public static function totalCost(): array
    {
        return [
            'TotalCostNetWithCollection' => 20.0,
            'TotalCostNetWithoutCollection' => 17.5,
            'TotalCostGrossWithCollection' => 24.0,
            'TotalCostGrossWithoutCollection' => 21.0,
        ];
    }

    public static function serviceResultCollectionOption(): array
    {
        return [
            'CollectionOptionID' => 3,
            'CollectionOptionTitle' => 'Driver collection',
            'CollectionCharge' => 2.5,
            'SameDayCollectionCutOffTime' => '12:00',
            'ExpectedLabel' => self::expectedLabel(),
        ];
    }

    public static function serviceQuoteResult(): array
    {
        return [
            'ServiceID' => 1234,
            'ServiceName' => 'Express Saver',
            'CarrierName' => 'Example Carrier',
            'ChargeableWeight' => 2.5,
            'TransitTimeEstimate' => '1-2 days',
            'SameDayCollectionCutOffTime' => '12:00',
            'IsWarehouseService' => false,
            'TotalCost' => self::totalCost(),
            'ServicePriceBreakdown' => [self::priceElement()],
            'OptionalExtras' => [['Code' => 'SIG', 'Description' => 'Signature on delivery', 'Cost' => 1.5]],
            'SignatureRequiredAvailable' => true,
            'ExpectedLabels' => [self::expectedLabel()],
            'CollectionOptions' => [self::serviceResultCollectionOption()],
            'ServiceType' => 'AirAndRoad',
        ];
    }

    public static function quoteSelection(): array
    {
        return ['QuoteID' => 98765];
    }

    public static function credentials(): array
    {
        return ['APIKey' => 'test-key', 'Password' => 'test-password'];
    }
}
