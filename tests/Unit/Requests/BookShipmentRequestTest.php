<?php

use ZachGilbert\TransglobalExpress\Dto\BookDetails;
use ZachGilbert\TransglobalExpress\Dto\QuoteSelection;
use ZachGilbert\TransglobalExpress\Dto\Shipment;
use ZachGilbert\TransglobalExpress\Requests\BookShipmentRequest;
use ZachGilbert\TransglobalExpress\Tests\Fixtures;

it('posts to the BookShipment endpoint', function () {
    $request = new BookShipmentRequest(new Shipment(), new BookDetails());

    expect($request->method())->toBe('POST')
        ->and($request->uri())->toBe('/Book/V2/BookShipment');
});

it('books a new shipment', function () {
    $request = new BookShipmentRequest(
        Shipment::fromArray(Fixtures::shipment()),
        BookDetails::fromArray(Fixtures::bookDetails()),
    );

    expect(encodedBody($request))->toBe([
        'Shipment' => Fixtures::shipment(),
        'BookDetails' => Fixtures::bookDetails(),
    ]);
});

it('books from an existing quote', function () {
    $request = new BookShipmentRequest(
        new QuoteSelection(quoteId: 98765),
        BookDetails::fromArray(Fixtures::bookDetails()),
    );

    expect(encodedBody($request))->toBe([
        'QuoteSelection' => ['QuoteID' => 98765],
        'BookDetails' => Fixtures::bookDetails(),
    ]);
});
