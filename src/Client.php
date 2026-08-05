<?php

namespace ZachGilbert\TransglobalExpress;

use GuzzleHttp\Psr7\HttpFactory;
use JsonException;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use ZachGilbert\TransglobalExpress\Concerns\Request;
use ZachGilbert\TransglobalExpress\Dto\Credentials;
use ZachGilbert\TransglobalExpress\Exceptions\{
    ApiException,
    MethodNotAllowedException,
    NetworkException,
    UnauthenticatedException,
    ValidationException,
};
use ZachGilbert\TransglobalExpress\Dto\Notification;
use ZachGilbert\TransglobalExpress\Resources\Quote;
use ZachGilbert\TransglobalExpress\Resources\Shipment;

final class Client
{
    /**
     * @param ClientInterface         $http
     * @param RequestFactoryInterface $requestFactory
     * @param Config                  $config
     * @param LoggerInterface|null    $logger
     */
    public function __construct(
        private readonly ClientInterface         $http,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly Config                  $config,
        private readonly ?LoggerInterface        $logger = null,
    ) {
    }

    /**
     * @param Config               $config
     * @param LoggerInterface|null $logger
     *
     * @return self
     */
    public static function make(Config $config, ?LoggerInterface $logger = null): self
    {
        return new self(
            http: new \GuzzleHttp\Client([
                'base_uri' => $config->baseUri(),
                'timeout' => 30,
            ]),
            requestFactory: new HttpFactory(),
            config: $config,
            logger: $logger,
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

        if (!is_null($this->logger)) {
            $this->logger->debug((string) $response->getBody(), [
                'endpoint' => $apiRequest->uri(),
                'method' => $apiRequest->method(),
                'status' => $response->getStatusCode(),
            ]);
        }

        if ($response->getStatusCode() >= 400) {
            $this->throwForHttpStatus($response);
        }

        $decoded = $this->decodeBody($response);

        if (array_key_exists('Status', $decoded) && $decoded['Status'] === 'FAIL') { // TODO - Enum
            $this->throwForBodyStatus($response);
        }

        return $decoded;
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
    private function throwForHttpStatus(ResponseInterface $response): never
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
     * @param ResponseInterface $response
     *
     * @return never
     */
    private function throwForBodyStatus(ResponseInterface $response): never
    {
        $raw = (string) $response->getBody();

        try {
            $body = json_decode($raw, true, flags: JSON_THROW_ON_ERROR);
            $body = is_array($body) ? $body : ['raw_body' => $raw];
        } catch (JsonException) {
            // Error response wasn't JSON at all (HTML error page, plain text, empty body)
            $body = ['raw_body' => $raw];
        }

        $message = '';

        if (array_key_exists('Status', $body) && is_string($body['Status'])) {
            $message .= "[{$body['Status']}] ";
        }

        if (array_key_exists('Notifications', $body) && is_array($body['Notifications'])) {
            $notifications = array_map(
                fn ($notification): Notification => Notification::fromArray($notification),
                $body['Notifications'],
            );

            $messages = [];

            foreach ($notifications as $notification) {
                $messages[] = $notification->message;
            }

            $message .= !empty($messages) ? implode('; ', $messages) : 'Something went wrong.';
        }

        throw new ApiException($message, 400, $body);
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
