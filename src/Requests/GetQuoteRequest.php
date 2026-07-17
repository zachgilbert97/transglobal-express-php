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

    public function method(): string
    {
        return 'GET';
    }

    public function uri(): string
    {
        return '/Quote/V2/GetQuote';
    }

    public function body(): array
    {
        return [
            'Shipment' => $this->shipment->toArray(),
            'Insurance' => $this->insurance->toArray(),
        ];
    }
}