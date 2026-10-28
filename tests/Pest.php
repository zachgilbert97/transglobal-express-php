<?php

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\HttpFactory;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\AssertionFailedError;
use Psr\Log\LoggerInterface;
use ZachGilbert\TransglobalExpress\Client;
use ZachGilbert\TransglobalExpress\Concerns\Request;
use ZachGilbert\TransglobalExpress\Config;
use ZachGilbert\TransglobalExpress\LoggingOptions;

/**
 * Build a Client whose HTTP layer replays queued responses instead of hitting the network.
 *
 * @param array<Response|Throwable>                                       $queue   Responses (or exceptions) returned in order
 * @param array<int, array{request: \Psr\Http\Message\RequestInterface}> $history Populated with every request sent
 * @param LoggerInterface|null                                            $logger
 * @param Config|null                                                     $config
 * @param LoggingOptions|null                                             $loggingOptions
 *
 * @return Client
 */
function fakeClient(
    array $queue,
    array &$history = [],
    ?LoggerInterface $logger = null,
    ?Config $config = null,
    ?LoggingOptions $loggingOptions = null,
): Client {
    $config ??= Config::staging('test-key', 'test-password');

    $stack = HandlerStack::create(new MockHandler($queue));
    $stack->push(Middleware::history($history));

    return new Client(
        http: new GuzzleClient(['handler' => $stack, 'base_uri' => $config->baseUri()]),
        requestFactory: new HttpFactory(),
        config: $config,
        logger: $logger,
        loggingOptions: $loggingOptions ?? new LoggingOptions(),
    );
}

/**
 * @param array                $body
 * @param int                  $status
 * @param array<string,string> $headers
 *
 * @return Response
 */
function jsonResponse(array $body, int $status = 200, array $headers = []): Response
{
    return new Response($status, ['Content-Type' => 'application/json', ...$headers], json_encode($body));
}

/**
 * Decode the JSON body of a request captured by fakeClient().
 *
 * @param array $history
 * @param int   $index
 *
 * @return array
 */
function sentBody(array $history, int $index = 0): array
{
    return json_decode((string) $history[$index]['request']->getBody(), true, flags: JSON_THROW_ON_ERROR);
}

/**
 * Encode a value to JSON and decode it back to an array.
 *
 * Whole-number floats keep their fraction so results can be compared strictly against fixtures.
 *
 * @param mixed $value
 *
 * @return array
 */
function jsonRoundTrip(mixed $value): array
{
    return json_decode(
        json_encode($value, JSON_THROW_ON_ERROR | JSON_PRESERVE_ZERO_FRACTION),
        true,
        flags: JSON_THROW_ON_ERROR,
    );
}

/**
 * The request body as it will be sent over the wire, decoded back to an array.
 *
 * @param Request $request
 *
 * @return array
 */
function encodedBody(Request $request): array
{
    return jsonRoundTrip($request->body());
}

/**
 * @param array $body
 *
 * @return Request
 */
function fakeRequest(array $body = []): Request
{
    return new class ($body) implements Request {
        public function __construct(private readonly array $body)
        {
        }

        public function method(): string
        {
            return 'POST';
        }

        public function uri(): string
        {
            return '/Test/V2/Endpoint';
        }

        public function body(): array
        {
            return $this->body;
        }
    };
}

/**
 * Run the callback and return whatever it throws, failing the test if nothing is thrown.
 *
 * @param callable $callback
 *
 * @return Throwable
 */
function captureException(callable $callback): Throwable
{
    try {
        $callback();
    } catch (Throwable $exception) {
        return $exception;
    }

    throw new AssertionFailedError('Expected an exception to be thrown, but none was.');
}
