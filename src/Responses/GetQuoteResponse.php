<?php

namespace ZachGilbert\TransglobalExpress\Responses;

use ZachGilbert\TransglobalExpress\Concerns\Response;
use ZachGilbert\TransglobalExpress\Dto\Quote;

final class GetQuoteResponse implements Response
{
    public function __construct(
        private readonly Quote $quote,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            quote: Quote::fromArray($data),
        );
    }

    public function quote(): Quote
    {
        return $this->quote;
    }
}