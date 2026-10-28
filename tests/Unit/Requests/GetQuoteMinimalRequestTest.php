<?php

use ZachGilbert\TransglobalExpress\Dto\Address;
use ZachGilbert\TransglobalExpress\Dto\Consignment;
use ZachGilbert\TransglobalExpress\Dto\Shipment;
use ZachGilbert\TransglobalExpress\Enums\ItemType;
use ZachGilbert\TransglobalExpress\Requests\GetQuoteMinimalRequest;
use ZachGilbert\TransglobalExpress\Tests\Fixtures;

it('posts to the GetQuoteMinimal endpoint', function () {
    $request = new GetQuoteMinimalRequest(new Shipment());

    expect($request->method())->toBe('POST')
        ->and($request->uri())->toBe('/Quote/V2/GetQuoteMinimal');
});

it('only sends the fields needed for a minimal quote', function () {
    $request = new GetQuoteMinimalRequest(Shipment::fromArray(Fixtures::shipment()));

    expect(encodedBody($request))->toBe([
        'Shipment' => [
            'Consignment' => [
                'ItemType' => 'Parcel',
                'Packages' => [
                    ['Weight' => 2.5, 'Length' => 30.0, 'Width' => 20.0, 'Height' => 10.0],
                ],
            ],
            'CollectionAddress' => [
                'City' => 'London',
                'Postcode' => 'SW1A 1AA',
                'Country' => Fixtures::country(),
            ],
            'DeliveryAddress' => [
                'City' => 'Paris',
                'Postcode' => '75001',
                'Country' => ['CountryID' => 74, 'Title' => 'France', 'CountryCode' => 'FR'],
            ],
        ],
    ]);
});

it('omits parts of the shipment that are not set', function () {
    $request = new GetQuoteMinimalRequest(new Shipment(
        consignment: new Consignment(itemType: ItemType::Document),
        deliveryAddress: new Address(postcode: '75001'),
    ));

    expect(encodedBody($request))->toBe([
        'Shipment' => [
            'Consignment' => ['ItemType' => 'Document'],
            'DeliveryAddress' => ['Postcode' => '75001'],
        ],
    ]);
});

it('drops a consignment and addresses that have none of the minimal fields', function () {
    $request = new GetQuoteMinimalRequest(new Shipment(
        consignment: new Consignment(itemsAreStackable: true, consignmentValue: 125.0, packages: []),
        collectionAddress: new Address(forename: 'Jane', emailAddress: 'jane@example.com'),
        deliveryAddress: new Address(postcode: '75001'),
    ));

    expect(json_encode($request->body()))->toBe('{"Shipment":{"DeliveryAddress":{"Postcode":"75001"}}}');
});

it('drops an empty packages list', function () {
    $request = new GetQuoteMinimalRequest(new Shipment(
        consignment: new Consignment(itemType: ItemType::Parcel, packages: []),
    ));

    expect(json_encode($request->body()))->toBe('{"Shipment":{"Consignment":{"ItemType":"Parcel"}}}');
});
