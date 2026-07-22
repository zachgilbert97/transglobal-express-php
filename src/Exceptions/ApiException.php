<?php

namespace ZachGilbert\TransglobalExpress\Exceptions;

use RuntimeException;
use Throwable;

class ApiException extends RuntimeException
{
    public function __construct(
        string                   $message,
        protected readonly int   $statusCode = 0,
        protected readonly array $responseBody = [],
        ?Throwable               $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function statusCode(): int
    {
        return $this->statusCode;
    }

    public function responseBody(): array
    {
        return $this->responseBody;
    }
}
