<?php

namespace ZachGilbert\TransglobalExpress;

final class LoggingOptions
{
    /**
     * @param bool         $logBodies  Log the (redacted) request and response bodies at debug level.
     *                                 Disable to keep only the one-line summaries, e.g. in production.
     * @param list<string> $redactKeys Additional field names (case-insensitive, at any depth) whose values
     *                                 are replaced with "[REDACTED]", e.g. ['EmailAddress', 'TelephoneNumber'].
     *                                 Credentials are always redacted.
     */
    public function __construct(
        public readonly bool $logBodies = true,
        public readonly array $redactKeys = [],
    ) {
    }
}
