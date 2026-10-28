<?php

use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request as HttpRequest;
use GuzzleHttp\Psr7\Response;
use ZachGilbert\TransglobalExpress\Dto\Insurance;
use ZachGilbert\TransglobalExpress\Dto\Shipment;
use ZachGilbert\TransglobalExpress\Exceptions\ApiException;
use ZachGilbert\TransglobalExpress\Exceptions\MethodNotAllowedException;
use ZachGilbert\TransglobalExpress\Exceptions\NetworkException;
use ZachGilbert\TransglobalExpress\Exceptions\UnauthenticatedException;
use ZachGilbert\TransglobalExpress\Exceptions\ValidationException;
use ZachGilbert\TransglobalExpress\Requests\GetQuoteRequest;

describe('sending requests', function () {
    it('sends a JSON request to the endpoint using the request method', function () {
        $history = [];

        fakeClient([jsonResponse(['Status' => 'SUCCESS'])], $history)->send(fakeRequest());

        $request = $history[0]['request'];

        expect($request->getMethod())->toBe('POST')
            ->and($request->getUri()->getPath())->toBe('/Test/V2/Endpoint')
            ->and($request->getHeaderLine('Content-Type'))->toBe('application/json');
    });

    it('adds the configured credentials to the request body', function () {
        $history = [];

        fakeClient([jsonResponse([])], $history)->send(fakeRequest(['Foo' => 'Bar']));

        expect(sentBody($history))->toBe([
            'Credentials' => ['APIKey' => 'test-key', 'Password' => 'test-password'],
            'Foo' => 'Bar',
        ]);
    });

    it('sends empty DTOs as JSON objects', function () {
        $history = [];

        fakeClient([jsonResponse([])], $history)->send(new GetQuoteRequest(new Shipment(), new Insurance()));

        expect((string) $history[0]['request']->getBody())->toBe(
            '{"Credentials":{"APIKey":"test-key","Password":"test-password"},"Shipment":{},"Insurance":{}}'
        );
    });

    it('returns the decoded body for non-failing statuses', function (string $status) {
        $body = ['Status' => $status, 'QuoteID' => 98765];

        expect(fakeClient([jsonResponse($body)])->send(fakeRequest()))->toBe($body);
    })->with(['SUCCESS', 'PARTIAL']);
});

describe('API-level failures', function () {
    it('throws when the response body has a FAIL status', function () {
        $body = [
            'Status' => 'FAIL',
            'Notifications' => [
                ['Message' => 'Postcode is invalid', 'Severity' => 'ERROR'],
                ['Message' => 'Weight is required', 'Severity' => 'ERROR'],
            ],
        ];

        $exception = captureException(fn () => fakeClient([jsonResponse($body)])->send(fakeRequest()));

        expect($exception::class)->toBe(ApiException::class)
            ->and($exception->getMessage())->toBe('[FAIL] Postcode is invalid; Weight is required')
            ->and($exception->statusCode())->toBe(400)
            ->and($exception->responseBody())->toBe($body);
    });

    it('uses a generic message when a FAIL response has no notifications', function () {
        $response = jsonResponse(['Status' => 'FAIL', 'Notifications' => []]);

        $exception = captureException(fn () => fakeClient([$response])->send(fakeRequest()));

        expect($exception->getMessage())->toBe('[FAIL] Something went wrong.');
    });
});

describe('HTTP errors', function () {
    it('maps HTTP error statuses to exceptions', function (int $status, string $exceptionClass) {
        $response = jsonResponse(['Message' => 'Request rejected'], $status);

        $exception = captureException(fn () => fakeClient([$response])->send(fakeRequest()));

        expect($exception::class)->toBe($exceptionClass)
            ->and($exception->getMessage())->toBe('Request rejected')
            ->and($exception->statusCode())->toBe($status)
            ->and($exception->getCode())->toBe($status);
    })->with([
        'unauthenticated' => [401, UnauthenticatedException::class],
        'method not allowed' => [405, MethodNotAllowedException::class],
        'validation' => [422, ValidationException::class],
        'not found' => [404, ApiException::class],
        'server error' => [500, ApiException::class],
    ]);

    it('falls back to a generic message and the raw body for non-JSON error responses', function () {
        $response = new Response(502, [], '<html>Bad Gateway</html>');

        $exception = captureException(fn () => fakeClient([$response])->send(fakeRequest()));

        expect($exception->getMessage())->toBe('API request failed with status 502')
            ->and($exception->responseBody())->toBe(['raw_body' => '<html>Bad Gateway</html>']);
    });

    it('includes the allowed methods from a 405 response', function () {
        $response = jsonResponse([], 405, ['Allow' => 'GET, OPTIONS']);

        $exception = captureException(fn () => fakeClient([$response])->send(fakeRequest()));

        expect($exception->responseBody()['allowed_methods'])->toBe(['GET', 'OPTIONS']);
    });

    it('wraps transport failures in a NetworkException', function () {
        $connectException = new ConnectException('Connection refused', new HttpRequest('POST', '/'));

        $exception = captureException(fn () => fakeClient([$connectException])->send(fakeRequest()));

        expect($exception)->toBeInstanceOf(NetworkException::class)
            ->and($exception->getMessage())->toBe('Could not connect to API: Connection refused')
            ->and($exception->getPrevious())->toBe($connectException);
    });
});

describe('malformed successful responses', function () {
    it('throws an ApiException when the body is not JSON', function () {
        $exception = captureException(fn () => fakeClient([new Response(200, [], 'OK')])->send(fakeRequest()));

        expect($exception::class)->toBe(ApiException::class)
            ->and($exception->getMessage())->toBe('API returned a non-JSON response body')
            ->and($exception->responseBody())->toBe(['raw_body' => 'OK'])
            ->and($exception->getPrevious())->toBeInstanceOf(JsonException::class);
    });

    it('throws an ApiException when the body is a JSON scalar', function () {
        $exception = captureException(fn () => fakeClient([new Response(200, [], '"ok"')])->send(fakeRequest()));

        expect($exception::class)->toBe(ApiException::class)
            ->and($exception->getMessage())->toBe('API response body was valid JSON but not an object/array')
            ->and($exception->responseBody())->toBe(['raw_body' => '"ok"']);
    });
});
