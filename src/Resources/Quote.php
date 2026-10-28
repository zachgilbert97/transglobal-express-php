<?php

namespace ZachGilbert\TransglobalExpress\Resources;

use JsonException;
use ZachGilbert\TransglobalExpress\Client;
use ZachGilbert\TransglobalExpress\Dto\Insurance;
use ZachGilbert\TransglobalExpress\Dto\Shipment;
use ZachGilbert\TransglobalExpress\Requests\GetQuoteMinimalRequest;
use ZachGilbert\TransglobalExpress\Requests\GetQuoteRequest;
use ZachGilbert\TransglobalExpress\Responses\GetQuoteMinimalResponse;
use ZachGilbert\TransglobalExpress\Responses\GetQuoteResponse;

class Quote
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    /**
     * @param Shipment       $shipment
     * @param Insurance|null $insurance
     *
     * @return GetQuoteResponse
     * @throws JsonException
     */
    public function get(Shipment $shipment, ?Insurance $insurance = null): GetQuoteResponse
    {
        return GetQuoteResponse::fromArray(
            $this->client->send(new GetQuoteRequest($shipment, $insurance))
        );
    }

    /**
     * @param Shipment $shipment
     *
     * @return GetQuoteMinimalResponse
     * @throws JsonException
     */
    public function getMinimal(Shipment $shipment): GetQuoteMinimalResponse
    {
        return GetQuoteMinimalResponse::fromArray(
            $this->client->Send(new GetQuoteMinimalRequest($shipment))
        );
    }
}
