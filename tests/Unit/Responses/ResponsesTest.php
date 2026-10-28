<?php

use ZachGilbert\TransglobalExpress\Dto\Country;
use ZachGilbert\TransglobalExpress\Dto\Document;
use ZachGilbert\TransglobalExpress\Dto\Label;
use ZachGilbert\TransglobalExpress\Dto\Notification;
use ZachGilbert\TransglobalExpress\Dto\OrderInvoice;
use ZachGilbert\TransglobalExpress\Dto\ServiceQuoteResult;
use ZachGilbert\TransglobalExpress\Enums\ResponseStatus;
use ZachGilbert\TransglobalExpress\Responses\BookShipmentResponse;
use ZachGilbert\TransglobalExpress\Responses\GetCountriesResponse;
use ZachGilbert\TransglobalExpress\Responses\GetQuoteMinimalResponse;
use ZachGilbert\TransglobalExpress\Responses\GetQuoteResponse;
use ZachGilbert\TransglobalExpress\Tests\Fixtures;

dataset('responses', [
    GetQuoteResponse::class,
    GetQuoteMinimalResponse::class,
    BookShipmentResponse::class,
    GetCountriesResponse::class,
]);

it('maps the status and notifications', function (string $responseClass, string $status, ResponseStatus $expected) {
    $response = $responseClass::fromArray([
        'Status' => $status,
        'Notifications' => [Fixtures::notification()],
    ]);

    expect($response->status)->toBe($expected)
        ->and($response->notifications)->toHaveCount(1)
        ->and($response->notifications[0])->toBeInstanceOf(Notification::class)
        ->and($response->notifications[0]->toArray())->toBe(Fixtures::notification());
})->with('responses')->with([
    'success' => ['SUCCESS', ResponseStatus::SUCCESS],
    'partial' => ['PARTIAL', ResponseStatus::PARTIAL],
]);

it('keeps an unrecognised status as a raw string', function (string $responseClass) {
    expect($responseClass::fromArray(['Status' => 'MAINTENANCE'])->status)->toBe('MAINTENANCE');
})->with('responses');

it('defaults missing fields to null', function (string $responseClass) {
    $response = $responseClass::fromArray([]);

    expect($response->status)->toBeNull()
        ->and($response->notifications)->toBeNull();
})->with('responses');

it('maps quote results', function (string $responseClass) {
    $response = $responseClass::fromArray([
        'QuoteID' => 98765,
        'ServiceResults' => [Fixtures::serviceQuoteResult()],
    ]);

    expect($response->quoteId)->toBe(98765)
        ->and($response->serviceResults)->toHaveCount(1)
        ->and($response->serviceResults[0])->toBeInstanceOf(ServiceQuoteResult::class)
        ->and($response->serviceResults[0]->toArray())->toBe(Fixtures::serviceQuoteResult());
})->with([GetQuoteResponse::class, GetQuoteMinimalResponse::class]);

it('maps a booked shipment', function () {
    $response = BookShipmentResponse::fromArray([
        'OrderReference' => 'TGE123456',
        'OrderInvoice' => Fixtures::orderInvoice(),
        'Labels' => [Fixtures::label()],
        'Documents' => [Fixtures::document()],
        'TrackingURL' => 'https://example.com/track/TGE123456',
        'FailReference' => 'FAIL-1',
    ]);

    expect($response->orderReference)->toBe('TGE123456')
        ->and($response->orderInvoice)->toBeInstanceOf(OrderInvoice::class)
        ->and($response->orderInvoice->toArray())->toBe(Fixtures::orderInvoice())
        ->and($response->labels[0])->toBeInstanceOf(Label::class)
        ->and($response->labels[0]->toArray())->toBe(Fixtures::label())
        ->and($response->documents[0])->toBeInstanceOf(Document::class)
        ->and($response->documents[0]->toArray())->toBe(Fixtures::document())
        ->and($response->trackingUrl)->toBe('https://example.com/track/TGE123456')
        ->and($response->failReference)->toBe('FAIL-1');
});

it('maps countries', function () {
    $france = Fixtures::country(['CountryID' => 74, 'Title' => 'France', 'CountryCode' => 'FR']);

    $response = GetCountriesResponse::fromArray(['Countries' => [Fixtures::country(), $france]]);

    expect($response->countries)->toHaveCount(2)
        ->and($response->countries)->each->toBeInstanceOf(Country::class)
        ->and($response->countries[1]->toArray())->toBe($france);
});

it('reads country IDs from the documented GetCountries response', function () {
    $response = GetCountriesResponse::fromArray([
        'Status' => 'SUCCESS',
        'Notifications' => [],
        'Countries' => [
            ['CountryID' => 231, 'Title' => 'United Kingdom (Mainland)', 'CountryCode' => 'GB'],
            ['CountryID' => 205, 'Title' => 'Spain (Mainland)', 'CountryCode' => 'ES'],
        ],
    ]);

    expect($response->countries[0]->countryId)->toBe(231)
        ->and($response->countries[1]->countryId)->toBe(205);
});
