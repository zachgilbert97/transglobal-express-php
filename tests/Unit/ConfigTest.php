<?php

use ZachGilbert\TransglobalExpress\Config;
use ZachGilbert\TransglobalExpress\Enums\Environment;

it('creates a config for each environment', function (string $factory, Environment $environment, string $baseUri) {
    $config = Config::$factory('key', 'password');

    expect($config->apiKey)->toBe('key')
        ->and($config->password)->toBe('password')
        ->and($config->environment)->toBe($environment)
        ->and($config->baseUri())->toBe($baseUri);
})->with([
    'staging' => ['staging', Environment::Staging, 'https://staging2.services3.transglobalexpress.co.uk'],
    'live' => ['live', Environment::Live, 'https://services3.transglobalexpress.co.uk'],
]);
