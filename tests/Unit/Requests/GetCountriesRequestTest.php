<?php

use ZachGilbert\TransglobalExpress\Requests\GetCountriesRequest;

it('posts an empty body to the GetCountries endpoint', function () {
    $request = new GetCountriesRequest();

    expect($request->method())->toBe('POST')
        ->and($request->uri())->toBe('/Country/V2/GetCountries')
        ->and($request->body())->toBe([]);
});
