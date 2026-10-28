<?php

use ZachGilbert\TransglobalExpress\Dto\BookDetails;
use ZachGilbert\TransglobalExpress\Dto\Insurance;
use ZachGilbert\TransglobalExpress\Dto\QuoteSelection;
use ZachGilbert\TransglobalExpress\Dto\Shipment;
use ZachGilbert\TransglobalExpress\Responses\BookShipmentResponse;
use ZachGilbert\TransglobalExpress\Responses\GetCountriesResponse;
use ZachGilbert\TransglobalExpress\Responses\GetQuoteMinimalResponse;
use ZachGilbert\TransglobalExpress\Responses\GetQuoteResponse;
use ZachGilbert\TransglobalExpress\Tests\Fixtures;

it('gets a quote', function () {
    $history = [];
    $client = fakeClient([jsonResponse(['Status' => 'SUCCESS', 'QuoteID' => 98765])], $history);

    $response = $client->quote()->get(
        Shipment::fromArray(Fixtures::shipment()),
        Insurance::fromArray(Fixtures::insurance()),
    );

    expect($history[0]['request']->getUri()->getPath())->toBe('/Quote/V2/GetQuote')
        ->and(sentBody($history))->toHaveKeys(['Credentials', 'Shipment', 'Insurance'])
        ->and($response)->toBeInstanceOf(GetQuoteResponse::class)
        ->and($response->quoteId)->toBe(98765);
});

it('gets a quote without insurance', function () {
    $history = [];
    $client = fakeClient([jsonResponse(['Status' => 'SUCCESS', 'QuoteID' => 98765])], $history);

    $client->quote()->get(Shipment::fromArray(Fixtures::shipment()));

    expect(sentBody($history))->toHaveKeys(['Credentials', 'Shipment'])
        ->and(sentBody($history))->not->toHaveKey('Insurance');
});

it('gets a minimal quote', function () {
    $history = [];
    $client = fakeClient([jsonResponse(['Status' => 'SUCCESS', 'QuoteID' => 98765])], $history);

    $response = $client->quote()->getMinimal(Shipment::fromArray(Fixtures::shipment()));

    expect($history[0]['request']->getUri()->getPath())->toBe('/Quote/V2/GetQuoteMinimal')
        ->and(sentBody($history))->toHaveKeys(['Credentials', 'Shipment'])
        ->and($response)->toBeInstanceOf(GetQuoteMinimalResponse::class)
        ->and($response->quoteId)->toBe(98765);
});

it('books a shipment', function () {
    $history = [];
    $client = fakeClient([jsonResponse(['Status' => 'SUCCESS', 'OrderReference' => 'TGE123456'])], $history);

    $response = $client->shipment()->book(
        new QuoteSelection(quoteId: 98765),
        BookDetails::fromArray(Fixtures::bookDetails()),
    );

    expect($history[0]['request']->getUri()->getPath())->toBe('/Book/V2/BookShipment')
        ->and(sentBody($history))->toHaveKeys(['Credentials', 'QuoteSelection', 'BookDetails'])
        ->and($response)->toBeInstanceOf(BookShipmentResponse::class)
        ->and($response->orderReference)->toBe('TGE123456');
});

it('gets the list of countries', function () {
    $history = [];
    $client = fakeClient([jsonResponse(['Status' => 'SUCCESS', 'Countries' => [Fixtures::country()]])], $history);

    $response = $client->countries()->get();

    expect($history[0]['request']->getUri()->getPath())->toBe('/Country/V2/GetCountries')
        ->and(sentBody($history))->toBe(['Credentials' => Fixtures::credentials()])
        ->and($response)->toBeInstanceOf(GetCountriesResponse::class)
        ->and($response->countries[0]->countryCode)->toBe('GB');
});
