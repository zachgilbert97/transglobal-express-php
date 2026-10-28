<?php

use ZachGilbert\TransglobalExpress\Dto\Address;
use ZachGilbert\TransglobalExpress\Dto\BookAccessory;
use ZachGilbert\TransglobalExpress\Dto\BookDetails;
use ZachGilbert\TransglobalExpress\Dto\Collection;
use ZachGilbert\TransglobalExpress\Dto\CommodityLine;
use ZachGilbert\TransglobalExpress\Dto\Consignment;
use ZachGilbert\TransglobalExpress\Dto\ConsignmentCurrency;
use ZachGilbert\TransglobalExpress\Dto\Country;
use ZachGilbert\TransglobalExpress\Dto\CPSCDeclaration;
use ZachGilbert\TransglobalExpress\Dto\Credentials;
use ZachGilbert\TransglobalExpress\Dto\Document;
use ZachGilbert\TransglobalExpress\Dto\ExpectedLabel;
use ZachGilbert\TransglobalExpress\Dto\Insurance;
use ZachGilbert\TransglobalExpress\Dto\InvoiceItem;
use ZachGilbert\TransglobalExpress\Dto\Label;
use ZachGilbert\TransglobalExpress\Dto\LabelSize;
use ZachGilbert\TransglobalExpress\Dto\Notification;
use ZachGilbert\TransglobalExpress\Dto\OrderInvoice;
use ZachGilbert\TransglobalExpress\Dto\Package;
use ZachGilbert\TransglobalExpress\Dto\PriceElement;
use ZachGilbert\TransglobalExpress\Dto\ProductIdentifiers;
use ZachGilbert\TransglobalExpress\Dto\QuoteSelection;
use ZachGilbert\TransglobalExpress\Dto\ServiceQuoteResult;
use ZachGilbert\TransglobalExpress\Dto\ServiceResultCollectionOption;
use ZachGilbert\TransglobalExpress\Dto\Shipment;
use ZachGilbert\TransglobalExpress\Dto\TotalCost;
use ZachGilbert\TransglobalExpress\Enums\CPSCDisclaimerType;
use ZachGilbert\TransglobalExpress\Enums\Currency;
use ZachGilbert\TransglobalExpress\Enums\DeclarationOption;
use ZachGilbert\TransglobalExpress\Enums\ItemType;
use ZachGilbert\TransglobalExpress\Enums\LabelGenerateStatus;
use ZachGilbert\TransglobalExpress\Enums\LabelRole;
use ZachGilbert\TransglobalExpress\Enums\LabelSize as LabelSizeEnum;
use ZachGilbert\TransglobalExpress\Enums\ServiceType;
use ZachGilbert\TransglobalExpress\Tests\Fixtures;

dataset('fully populated payloads', fn (): array => [
    'Address' => [Address::class, Fixtures::address()],
    'BookAccessory' => [BookAccessory::class, Fixtures::bookAccessory()],
    'BookDetails' => [BookDetails::class, Fixtures::bookDetails()],
    'Collection' => [Collection::class, Fixtures::collection()],
    'CommodityLine' => [CommodityLine::class, Fixtures::commodityLine()],
    'Consignment' => [Consignment::class, Fixtures::consignment()],
    'ConsignmentCurrency' => [ConsignmentCurrency::class, Fixtures::consignmentCurrency()],
    'Country' => [Country::class, Fixtures::country()],
    'CPSCDeclaration' => [CPSCDeclaration::class, Fixtures::cpscDeclaration()],
    'Credentials' => [Credentials::class, Fixtures::credentials()],
    'Document' => [Document::class, Fixtures::document()],
    'ExpectedLabel' => [ExpectedLabel::class, Fixtures::expectedLabel()],
    'Insurance' => [Insurance::class, Fixtures::insurance()],
    'InvoiceItem' => [InvoiceItem::class, Fixtures::invoiceItem()],
    'Label' => [Label::class, Fixtures::label()],
    'LabelSize' => [LabelSize::class, Fixtures::labelSize()],
    'Notification' => [Notification::class, Fixtures::notification()],
    'OrderInvoice' => [OrderInvoice::class, Fixtures::orderInvoice()],
    'Package' => [Package::class, Fixtures::package()],
    'PriceElement' => [PriceElement::class, Fixtures::priceElement()],
    'ProductIdentifiers' => [ProductIdentifiers::class, Fixtures::productIdentifiers()],
    'QuoteSelection' => [QuoteSelection::class, Fixtures::quoteSelection()],
    'ServiceQuoteResult' => [ServiceQuoteResult::class, Fixtures::serviceQuoteResult()],
    'ServiceResultCollectionOption' => [ServiceResultCollectionOption::class, Fixtures::serviceResultCollectionOption()],
    'Shipment' => [Shipment::class, Fixtures::shipment()],
    'TotalCost' => [TotalCost::class, Fixtures::totalCost()],
]);

it('round-trips a fully populated payload', function (string $dtoClass, array $payload) {
    expect($dtoClass::fromArray($payload)->toArray())->toBe($payload);
})->with('fully populated payloads');

it('encodes to JSON with the same structure as toArray()', function (string $dtoClass, array $payload) {
    $dto = $dtoClass::fromArray($payload);

    expect(jsonRoundTrip($dto))->toBe($dto->toArray());
})->with('fully populated payloads');

it('omits unset fields when serialising', function (string $dtoClass) {
    expect((new $dtoClass())->toArray())->toBe([])
        ->and($dtoClass::fromArray([])->toArray())->toBe([]);
})->with('optional DTOs');

it('encodes empty DTOs as JSON objects', function (string $dtoClass) {
    expect(json_encode(new $dtoClass()))->toBe('{}');
})->with('optional DTOs');

it('encodes nested empty DTOs as objects and empty lists as arrays', function () {
    $shipment = new Shipment(
        consignment: new Consignment(packages: []),
        deliveryAddress: new Address(),
    );

    expect(json_encode($shipment))->toBe('{"Consignment":{"Packages":[]},"DeliveryAddress":{}}');
});

dataset('optional DTOs', [
    Address::class,
    BookAccessory::class,
    BookDetails::class,
    Collection::class,
    CommodityLine::class,
    Consignment::class,
    ConsignmentCurrency::class,
    Country::class,
    CPSCDeclaration::class,
    Document::class,
    ExpectedLabel::class,
    Insurance::class,
    InvoiceItem::class,
    Label::class,
    LabelSize::class,
    Notification::class,
    OrderInvoice::class,
    Package::class,
    PriceElement::class,
    ProductIdentifiers::class,
    QuoteSelection::class,
    ServiceQuoteResult::class,
    ServiceResultCollectionOption::class,
    Shipment::class,
    TotalCost::class,
]);

it('keeps falsy values that are not null', function () {
    expect((new Insurance(coverValue: 0.0, goodsAreNew: false))->toArray())
        ->toBe(['CoverValue' => 0.0, 'GoodsAreNew' => false])
        ->and((new Package(weight: 0.0, commodityDetails: []))->toArray())
        ->toBe(['Weight' => 0.0, 'CommodityDetails' => []]);
});

it('hydrates known enum values into enum cases', function (string $dtoClass, string $key, string $property, int|string $value, BackedEnum $expected) {
    expect($dtoClass::fromArray([$key => $value])->{$property})->toBe($expected);
})->with([
    'Consignment item type' => [Consignment::class, 'ItemType', 'itemType', 'Pallet', ItemType::Pallet],
    'ConsignmentCurrency code' => [ConsignmentCurrency::class, 'CurrencyCode', 'currencyCode', 'EUR', Currency::EUR],
    'CPSCDeclaration option' => [CPSCDeclaration::class, 'DeclarationOption', 'declarationOption', 3, DeclarationOption::Exempt_or_Disclaimer],
    'CPSCDeclaration disclaimer' => [CPSCDeclaration::class, 'DisclaimerType', 'disclaimerType', 'B', CPSCDisclaimerType::B],
    'ExpectedLabel role' => [ExpectedLabel::class, 'LabelRole', 'labelRole', 'Collection', LabelRole::Collection],
    'ExpectedLabel status' => [ExpectedLabel::class, 'LabelGenerateStatus', 'labelGenerateStatus', 'Conditional', LabelGenerateStatus::Conditional],
    'Label size' => [Label::class, 'LabelSize', 'labelSize', 'A4', LabelSizeEnum::A4],
    'LabelSize size' => [LabelSize::class, 'Size', 'size', 'Thermal', LabelSizeEnum::Thermal],
    'ServiceQuoteResult type' => [ServiceQuoteResult::class, 'ServiceType', 'serviceType', 'Road', ServiceType::Road],
]);

it('keeps unrecognised enum values as raw values', function (string $dtoClass, string $key, string $property, int|string $value) {
    $dto = $dtoClass::fromArray([$key => $value]);

    expect($dto->{$property})->toBe($value)
        ->and($dto->toArray())->toBe([$key => $value]);
})->with([
    'Consignment item type' => [Consignment::class, 'ItemType', 'itemType', 'Crate'],
    'ConsignmentCurrency code' => [ConsignmentCurrency::class, 'CurrencyCode', 'currencyCode', 'XYZ'],
    'CPSCDeclaration option' => [CPSCDeclaration::class, 'DeclarationOption', 'declarationOption', 99],
    'CPSCDeclaration disclaimer' => [CPSCDeclaration::class, 'DisclaimerType', 'disclaimerType', 'Z'],
    'ExpectedLabel role' => [ExpectedLabel::class, 'LabelRole', 'labelRole', 'Return'],
    'ExpectedLabel status' => [ExpectedLabel::class, 'LabelGenerateStatus', 'labelGenerateStatus', 'Pending'],
    'Label size' => [Label::class, 'LabelSize', 'labelSize', 'A6'],
    'LabelSize size' => [LabelSize::class, 'Size', 'size', 'A6'],
    'ServiceQuoteResult type' => [ServiceQuoteResult::class, 'ServiceType', 'serviceType', 'Sea'],
]);

it('serialises enum cases passed to the constructor as their backing values', function () {
    $consignment = new Consignment(
        itemType: ItemType::Document,
        consignmentCurrency: new ConsignmentCurrency(Currency::USD),
    );

    expect($consignment->toArray())->toBe([
        'ItemType' => 'Document',
        'ConsignmentCurrency' => ['CurrencyCode' => 'USD'],
    ]);
});

it('strips illegal characters from download URLs', function (string $dtoClass) {
    expect($dtoClass::fromArray(['DownloadURL' => 'https://example.com/labels/my label.pdf'])->toArray())
        ->toBe(['DownloadURL' => 'https://example.com/labels/mylabel.pdf']);
})->with([Document::class, Label::class]);
