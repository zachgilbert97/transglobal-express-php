<?php

namespace ZachGilbert\TransglobalExpress\Requests;

use ZachGilbert\TransglobalExpress\Concerns\Request;
use ZachGilbert\TransglobalExpress\Dto\Insurance;
use ZachGilbert\TransglobalExpress\Dto\Shipment;

class GetQuoteRequest implements Request
{
    /**
     * @param Shipment  $shipment
     * @param Insurance $insurance
     */
    public function __construct(
        private readonly Shipment $shipment,
        private readonly Insurance $insurance,
    ) {
    }

    /**
     * @return string
     */
    public function method(): string
    {
        return 'POST';
    }

    /**
     * @return string
     */
    public function uri(): string
    {
        return '/Quote/V2/GetQuote';
    }

    /**
     * @return array
     */
    public function body(): array
    {
        return array_filter([
            'Shipment' => $this->shipment->toArray(),
            'Insurance' => $this->insurance->toArray(),
        ], static fn ($value): bool => !is_null($value));
    }
}
