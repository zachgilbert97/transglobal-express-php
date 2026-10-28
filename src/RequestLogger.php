<?php

namespace ZachGilbert\TransglobalExpress;

use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Throwable;
use ZachGilbert\TransglobalExpress\Concerns\Request;
use ZachGilbert\TransglobalExpress\Enums\Environment;
use ZachGilbert\TransglobalExpress\Enums\ResponseStatus;

/**
 * Writes a redacted record of each API call to a PSR-3 logger.
 *
 * Every call produces a one-line summary (info, warning or error depending on the outcome) and,
 * when enabled, a debug entry with the redacted request and response bodies. Both share a
 * request_id so they can be correlated.
 *
 * @internal
 */
final class RequestLogger
{
    private const REDACTED = '[REDACTED]';

    /**
     * Fields holding base64 file content, which is summarised rather than logged in full.
     */
    private const BASE64_KEYS = ['labelcontent', 'content'];

    private const MAX_RAW_BODY_LENGTH = 2000;

    /**
     * @var list<string>
     */
    private readonly array $redactKeys;

    /**
     * @param LoggerInterface $logger
     * @param Environment     $environment
     * @param LoggingOptions  $options
     */
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly Environment $environment,
        private readonly LoggingOptions $options,
    ) {
        $this->redactKeys = array_map('strtolower', ['Password', ...$options->redactKeys]);
    }

    /**
     * Log a call that received an HTTP response, successful or not.
     *
     * @param Request           $apiRequest
     * @param string            $requestBody The JSON body that was sent
     * @param ResponseInterface $response
     * @param string            $requestId
     * @param int               $durationMs
     *
     * @return void
     */
    public function logResponse(
        Request $apiRequest,
        string $requestBody,
        ResponseInterface $response,
        string $requestId,
        int $durationMs,
    ): void {
        $rawResponseBody = (string) $response->getBody();
        $responseBody = $this->decode($rawResponseBody);
        $httpStatus = $response->getStatusCode();
        $bodyStatus = is_string($responseBody['Status'] ?? null) ? $responseBody['Status'] : null;

        $this->logPayloads($requestId, [
            'request' => $this->loggableBody($requestBody),
            'response' => $this->loggableBody($rawResponseBody),
        ]);

        $level = match (true) {
            $httpStatus >= 500 => LogLevel::ERROR,
            $httpStatus >= 400, $bodyStatus === ResponseStatus::FAIL->value => LogLevel::WARNING,
            default => LogLevel::INFO,
        };

        $this->logger->log(
            $level,
            sprintf(
                '%s %s %s responded %d (%d ms)',
                $this->prefix(),
                $apiRequest->method(),
                $apiRequest->uri(),
                $httpStatus,
                $durationMs,
            ),
            array_filter([
                ...$this->baseContext($apiRequest, $requestId, $durationMs),
                'http_status' => $httpStatus,
                'response_status' => $bodyStatus,
                'order_reference' => $responseBody['OrderReference'] ?? null,
                'quote_id' => $responseBody['QuoteID'] ?? null,
                'notifications' => $this->notificationMessages($responseBody),
            ], static fn ($value): bool => !is_null($value)),
        );
    }

    /**
     * Log a call that failed before any HTTP response was received.
     *
     * @param Request   $apiRequest
     * @param string    $requestBody The JSON body that was sent
     * @param string    $requestId
     * @param int       $durationMs
     * @param Throwable $exception
     *
     * @return void
     */
    public function logNetworkFailure(
        Request $apiRequest,
        string $requestBody,
        string $requestId,
        int $durationMs,
        Throwable $exception,
    ): void {
        $this->logPayloads($requestId, ['request' => $this->loggableBody($requestBody)]);

        $this->logger->error(
            sprintf(
                '%s %s %s failed: %s (%d ms)',
                $this->prefix(),
                $apiRequest->method(),
                $apiRequest->uri(),
                $exception->getMessage(),
                $durationMs,
            ),
            [
                ...$this->baseContext($apiRequest, $requestId, $durationMs),
                'exception' => $exception,
            ],
        );
    }

    /**
     * @param string                $requestId
     * @param array<string, mixed>  $payloads
     *
     * @return void
     */
    private function logPayloads(string $requestId, array $payloads): void
    {
        if (!$this->options->logBodies) {
            return;
        }

        $this->logger->debug("{$this->prefix()} API payloads", ['request_id' => $requestId, ...$payloads]);
    }

    /**
     * @param Request $apiRequest
     * @param string  $requestId
     * @param int     $durationMs
     *
     * @return array{request_id: string, environment: string, method: string, endpoint: string, duration_ms: int}
     */
    private function baseContext(Request $apiRequest, string $requestId, int $durationMs): array
    {
        return [
            'request_id' => $requestId,
            'environment' => $this->environment->value,
            'method' => $apiRequest->method(),
            'endpoint' => $apiRequest->uri(),
            'duration_ms' => $durationMs,
        ];
    }

    /**
     * @return string
     */
    private function prefix(): string
    {
        return "Transglobal Express {$this->environment->value}";
    }

    /**
     * The redacted body as an array, or the truncated raw string when it is not a JSON object.
     *
     * @param string $raw
     *
     * @return array<mixed>|string
     */
    private function loggableBody(string $raw): array|string
    {
        $decoded = $this->decode($raw);

        if (!is_null($decoded)) {
            return $this->redact($decoded);
        }

        return strlen($raw) > self::MAX_RAW_BODY_LENGTH
            ? substr($raw, 0, self::MAX_RAW_BODY_LENGTH) . sprintf('… [truncated, %d bytes total]', strlen($raw))
            : $raw;
    }

    /**
     * @param string $raw
     *
     * @return array<mixed>|null
     */
    private function decode(string $raw): ?array
    {
        $decoded = json_decode($raw, true);

        return is_array($decoded) ? $decoded : null;
    }

    /**
     * @param array<mixed> $data
     *
     * @return array<mixed>
     */
    private function redact(array $data): array
    {
        foreach ($data as $key => $value) {
            $normalisedKey = is_string($key) ? strtolower($key) : null;

            $data[$key] = match (true) {
                $normalisedKey === 'apikey' && is_string($value) => $this->mask($value),
                in_array($normalisedKey, $this->redactKeys, true) => self::REDACTED,
                in_array($normalisedKey, self::BASE64_KEYS, true) && is_string($value) => sprintf(
                    '[base64, %.1f KB]',
                    strlen($value) / 1024,
                ),
                is_array($value) => $this->redact($value),
                default => $value,
            };
        }

        return $data;
    }

    /**
     * Keep the last four characters of long secrets so keys can be told apart in logs.
     *
     * @param string $secret
     *
     * @return string
     */
    private function mask(string $secret): string
    {
        return strlen($secret) > 8
            ? '****' . substr($secret, -4)
            : self::REDACTED;
    }

    /**
     * @param array<mixed>|null $responseBody
     *
     * @return list<string>|null
     */
    private function notificationMessages(?array $responseBody): ?array
    {
        if (!is_array($responseBody['Notifications'] ?? null)) {
            return null;
        }

        $messages = array_values(array_filter(
            array_map(
                static fn ($notification): mixed => is_array($notification) ? ($notification['Message'] ?? null) : null,
                $responseBody['Notifications'],
            ),
            static fn ($message): bool => is_string($message) && $message !== '',
        ));

        return $messages ?: null;
    }
}
