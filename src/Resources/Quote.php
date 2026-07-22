<?php

namespace ZachGilbert\TransglobalExpress\Resources;

use JsonException;
use ZachGilbert\TransglobalExpress\Client;
use ZachGilbert\TransglobalExpress\Dto\Insurance;
use ZachGilbert\TransglobalExpress\Dto\Shipment;
use ZachGilbert\TransglobalExpress\Requests\GetQuoteRequest;
use ZachGilbert\TransglobalExpress\Responses\GetQuoteResponse;

class Quote
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    /**
     * @param Shipment  $shipment
     * @param Insurance $insurance
     *
     * @return GetQuoteResponse
     * @throws JsonException
     */
    public function get(Shipment $shipment, Insurance $insurance): GetQuoteResponse
    {
        return GetQuoteResponse::fromArray(
            $this->client->send(new GetQuoteRequest($shipment, $insurance))
        );
    }
}
