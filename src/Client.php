<?php

namespace ZachGilbert\TransglobalExpress;

use GuzzleHttp\Psr7\HttpFactory;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use ZachGilbert\TransglobalExpress\Concerns\Request;
use ZachGilbert\TransglobalExpress\Dto\Credentials;
use ZachGilbert\TransglobalExpress\Exceptions\{
    ApiException,
    MethodNotAllowedException,
    NetworkException,
    UnauthenticatedException,
    ValidationException,
};
use ZachGilbert\TransglobalExpress\Resources\Quote;
use ZachGilbert\TransglobalExpress\Resources\Shipment;

final class Client
{
    /**
     * @param ClientInterface         $http
     * @param RequestFactoryInterface $requestFactory
     * @param Config                  $config
     */
    public function __construct(
        private readonly ClientInterface         $http,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly Config                  $config,
    ) {
    }

    /**
     * @param Config $config
     *
     * @return self
     */
    public static function make(Config $config): self
    {
        return new self(
            http: new \GuzzleHttp\Client([
                'base_uri' => $config->baseUri(),
                'timeout' => 30,
            ]),
            requestFactory: new HttpFactory(),
            config: $config,
        );
    }

    /**
     * @param Request $apiRequest
     *
     * @return array
     *
     * @throws JsonException
     */
    public function send(Request $apiRequest): array
    {
        $request = $this->requestFactory
            ->createRequest($apiRequest->method(), $apiRequest->uri())
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(
            json_encode([
                'Credentials' => (new Credentials($this->config->apiKey, $this->config->password))->toArray(),
                ...$apiRequest->body(),
            ], JSON_THROW_ON_ERROR)
        );

        try {
            $response = $this->http->sendRequest($request);
        } catch (ClientExceptionInterface $exception) {
            throw new NetworkException(
                message: 'Could not connect to API: ' . $exception->getMessage(),
                previous: $exception,
            );
        }

        if ($response->getStatusCode() >= 400) {
            $this->throwForResponse($response);
        }

        return $this->decodeBody($response);
    }

    /**
     * Decode a successful (2xx) response body.
     *
     * Any failure decoding here is still unexpected — surface it as an ApiException
     * rather than a raw JsonException, so consumers only ever need to catch one hierarchy.
     *
     * @param ResponseInterface $response
     *
     * @return array
     */
    private function decodeBody(ResponseInterface $response): array
    {
        $raw = (string) $response->getBody();

        try {
            $decoded = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ApiException(
                'API returned a non-JSON response body',
                $response->getStatusCode(),
                ['raw_body' => $raw],
                previous: $e,
            );
        }

        // json_decode(true) can legitimately return a scalar or null for
        // valid-but-unexpected JSON (e.g. body was just `null` or `"ok"`).
        if (!is_array($decoded)) {
            throw new ApiException(
                'API response body was valid JSON but not an object/array',
                $response->getStatusCode(),
                ['raw_body' => $raw],
            );
        }

        return $decoded;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return never
     */
    private function throwForResponse(ResponseInterface $response): never
    {
        $status = $response->getStatusCode();
        $raw = (string) $response->getBody();

        try {
            $body = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
            $body = is_array($body) ? $body : ['raw_body' => $raw];
        } catch (JsonException) {
            // Error response wasn't JSON at all (HTML error page, plain text, empty body)
            $body = ['raw_body' => $raw];
        }

        $message = $body['message'] ?? "API request failed with status {$status}";

        if ($status === 405) {
            $body['allowed_methods'] = array_map(
                'trim',
                explode(',', $response->getHeaderLine('Allow'))
            );
        }

        throw match ($status) {
            401 => new UnauthenticatedException($message, $status, $body),
            405 => new MethodNotAllowedException($message, $status, $body),
            422 => new ValidationException($message, $status, $body),
            default => new ApiException($message, $status, $body),
        };
    }

    /**
     * @return Quote
     */
    public function quote(): Quote
    {
        return new Quote($this);
    }

    /**
     * @return Shipment
     */
    public function shipment(): Shipment
    {
        return new Shipment($this);
    }
}
