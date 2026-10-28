<?php

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request as HttpRequest;
use GuzzleHttp\Psr7\Response;
use ZachGilbert\TransglobalExpress\Config;
use ZachGilbert\TransglobalExpress\Exceptions\ApiException;
use ZachGilbert\TransglobalExpress\LoggingOptions;
use ZachGilbert\TransglobalExpress\Tests\Fixtures;
use ZachGilbert\TransglobalExpress\Tests\Support\InMemoryLogger;

describe('summary', function () {
    it('logs a one-line summary of a successful request at info level', function () {
        $logger = new InMemoryLogger();

        fakeClient(
            [jsonResponse(['Status' => 'SUCCESS', 'QuoteID' => 98765, 'OrderReference' => 'TGE123456'])],
            logger: $logger,
        )->send(fakeRequest());

        $summary = $logger->recordsAt('info');

        expect($summary)->toHaveCount(1)
            ->and($summary[0]['message'])->toMatch('/^Transglobal Express Staging POST \/Test\/V2\/Endpoint responded 200 \(\d+ ms\)$/')
            ->and($summary[0]['context'])->toMatchArray([
                'environment' => 'Staging',
                'method' => 'POST',
                'endpoint' => '/Test/V2/Endpoint',
                'http_status' => 200,
                'response_status' => 'SUCCESS',
                'order_reference' => 'TGE123456',
                'quote_id' => 98765,
            ])
            ->and($summary[0]['context']['duration_ms'])->toBeInt()
            ->and($summary[0]['context']['request_id'])->toMatch('/^[0-9a-f]{16}$/');
    });

    it('logs the summary at a level matching the outcome', function (Response $response, string $level) {
        $logger = new InMemoryLogger();

        try {
            fakeClient([$response], logger: $logger)->send(fakeRequest());
        } catch (ApiException) {
            // Failing outcomes throw after logging; only the log level matters here.
        }

        expect($logger->recordsAt($level))->toHaveCount(1);
    })->with([
        'success' => [fn () => jsonResponse(['Status' => 'SUCCESS']), 'info'],
        'partial' => [fn () => jsonResponse(['Status' => 'PARTIAL']), 'info'],
        'FAIL status' => [fn () => jsonResponse(['Status' => 'FAIL']), 'warning'],
        'client error' => [fn () => jsonResponse([], 422), 'warning'],
        'server error' => [fn () => jsonResponse([], 500), 'error'],
    ]);

    it('includes notification messages in the summary', function () {
        $logger = new InMemoryLogger();
        $response = jsonResponse([
            'Status' => 'FAIL',
            'Notifications' => [['Message' => 'Postcode is invalid', 'Severity' => 'ERROR']],
        ]);

        captureException(fn () => fakeClient([$response], logger: $logger)->send(fakeRequest()));

        expect($logger->recordsAt('warning')[0]['context']['notifications'])->toBe(['Postcode is invalid']);
    });

    it('logs network failures at error level with the exception', function () {
        $logger = new InMemoryLogger();
        $connectException = new ConnectException('Connection refused', new HttpRequest('POST', '/'));

        captureException(fn () => fakeClient([$connectException], logger: $logger)->send(fakeRequest(['Foo' => 'Bar'])));

        $error = $logger->recordsAt('error');
        $payloads = $logger->recordsAt('debug');

        expect($error)->toHaveCount(1)
            ->and($error[0]['message'])->toMatch('/^Transglobal Express Staging POST \/Test\/V2\/Endpoint failed: Connection refused \(\d+ ms\)$/')
            ->and($error[0]['context']['exception'])->toBe($connectException)
            ->and($payloads[0]['context']['request']['Foo'])->toBe('Bar')
            ->and($payloads[0]['context'])->not->toHaveKey('response');
    });
});

describe('payloads', function () {
    it('logs the request and decoded response bodies at debug level with the same request id', function () {
        $logger = new InMemoryLogger();

        fakeClient([jsonResponse(['Status' => 'SUCCESS', 'QuoteID' => 98765])], logger: $logger)
            ->send(fakeRequest(['Foo' => 'Bar']));

        $payloads = $logger->recordsAt('debug');

        expect($payloads)->toHaveCount(1)
            ->and($payloads[0]['message'])->toBe('Transglobal Express Staging API payloads')
            ->and($payloads[0]['context']['request']['Foo'])->toBe('Bar')
            ->and($payloads[0]['context']['response'])->toBe(['Status' => 'SUCCESS', 'QuoteID' => 98765])
            ->and($payloads[0]['context']['request_id'])->toBe($logger->recordsAt('info')[0]['context']['request_id']);
    });

    it('never logs the API password and masks the API key', function () {
        $logger = new InMemoryLogger();
        $config = Config::staging('live-api-key-1a2b', 'super-secret-password');

        fakeClient([jsonResponse(['Status' => 'SUCCESS'])], logger: $logger, config: $config)->send(fakeRequest());

        expect($logger->recordsAt('debug')[0]['context']['request']['Credentials'])->toBe([
            'APIKey' => '****1a2b',
            'Password' => '[REDACTED]',
        ])
            ->and(json_encode($logger->records))->not->toContain('super-secret-password')
            ->and(json_encode($logger->records))->not->toContain('live-api-key-1a2b');
    });

    it('fully redacts short API keys', function () {
        $logger = new InMemoryLogger();

        fakeClient([jsonResponse([])], logger: $logger)->send(fakeRequest());

        expect($logger->recordsAt('debug')[0]['context']['request']['Credentials']['APIKey'])->toBe('[REDACTED]');
    });

    it('summarises base64 label and document content', function () {
        $logger = new InMemoryLogger();
        $label = [...Fixtures::label(), 'LabelContent' => str_repeat('A', 2048)];
        $document = [...Fixtures::document(), 'Content' => str_repeat('B', 512)];

        fakeClient([jsonResponse(['Labels' => [$label], 'Documents' => [$document]])], logger: $logger)
            ->send(fakeRequest());

        $response = $logger->recordsAt('debug')[0]['context']['response'];

        expect($response['Labels'][0]['LabelContent'])->toBe('[base64, 2.0 KB]')
            ->and($response['Labels'][0]['AirWaybillReference'])->toBe('AWB123456')
            ->and($response['Documents'][0]['Content'])->toBe('[base64, 0.5 KB]');
    });

    it('redacts configured keys at any depth', function () {
        $logger = new InMemoryLogger();

        fakeClient(
            [jsonResponse([])],
            logger: $logger,
            loggingOptions: new LoggingOptions(redactKeys: ['emailaddress']),
        )->send(fakeRequest(['Shipment' => ['DeliveryAddress' => Fixtures::address()]]));

        $address = $logger->recordsAt('debug')[0]['context']['request']['Shipment']['DeliveryAddress'];

        expect($address['EmailAddress'])->toBe('[REDACTED]')
            ->and($address['City'])->toBe('London');
    });

    it('logs non-JSON response bodies as truncated raw text', function () {
        $logger = new InMemoryLogger();
        $html = '<html>' . str_repeat('x', 3000) . '</html>';

        captureException(fn () => fakeClient([new Response(502, [], $html)], logger: $logger)->send(fakeRequest()));

        expect($logger->recordsAt('debug')[0]['context']['response'])
            ->toStartWith('<html>xxx')
            ->toEndWith('… [truncated, 3013 bytes total]');
    });

    it('skips payloads when body logging is disabled', function () {
        $logger = new InMemoryLogger();

        fakeClient([jsonResponse(['Status' => 'SUCCESS'])], logger: $logger, loggingOptions: new LoggingOptions(logBodies: false))
            ->send(fakeRequest());

        expect($logger->recordsAt('debug'))->toBeEmpty()
            ->and($logger->recordsAt('info'))->toHaveCount(1);
    });
});
