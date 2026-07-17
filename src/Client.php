<?php

namespace ZachGilbert\TransglobalExpress;

use JsonException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use ZachGilbert\TransglobalExpress\Concerns\Request;
use ZachGilbert\TransglobalExpress\Dto\Credentials;
use ZachGilbert\TransglobalExpress\Resources\Quote;

final class Client
{
    public function __construct(
        private readonly ClientInterface         $http,
        private readonly RequestFactoryInterface $requestFactory,
        private readonly Config                  $config,
    )
    {
    }

    public static function make(Config $config): self
    {
        return new self(
            http: new \GuzzleHttp\Client([
                'base_uri' => $config->baseUri(),
                'timeout' => 30,
            ]),
            requestFactory: new \GuzzleHttp\Psr7\HttpFactory(),
            config: $config,
        );
    }

    /**
     * @param Request $request
     *
     * @return ResponseInterface
     * @throws JsonException
     */
    public function send(Request $request): ResponseInterface
    {
        $request = $this->requestFactory
            ->createRequest($request->method(), $request->uri())
            ->withHeader('Content-Type', 'application/json');

        $request->getBody()->write(
            json_encode([
                'Credentials' => (new Credentials($this->config->apiKey, $this->config->password))->toArray(),
                ...$request->body(),
            ], JSON_THROW_ON_ERROR)
        );

        return $this->http->sendRequest($request);
    }

    /**
     * @return Quote
     */
    public function quote(): Quote
    {
        return new Quote($this);
    }
}