<?php

use ZachGilbert\TransglobalExpress\Dto\Insurance;
use ZachGilbert\TransglobalExpress\Dto\Shipment;
use ZachGilbert\TransglobalExpress\Requests\GetQuoteRequest;
use ZachGilbert\TransglobalExpress\Tests\Fixtures;

it('posts to the GetQuote endpoint', function () {
    $request = new GetQuoteRequest(new Shipment());

    expect($request->method())->toBe('POST')
        ->and($request->uri())->toBe('/Quote/V2/GetQuote');
});

it('sends the full shipment and insurance', function () {
    $request = new GetQuoteRequest(
        Shipment::fromArray(Fixtures::shipment()),
        Insurance::fromArray(Fixtures::insurance()),
    );

    expect(encodedBody($request))->toBe([
        'Shipment' => Fixtures::shipment(),
        'Insurance' => Fixtures::insurance(),
    ]);
});

it('omits insurance when none is given', function () {
    $request = new GetQuoteRequest(Shipment::fromArray(Fixtures::shipment()));

    expect(encodedBody($request))->toBe(['Shipment' => Fixtures::shipment()]);
});
