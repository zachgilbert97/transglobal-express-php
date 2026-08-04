<?php

namespace ZachGilbert\TransglobalExpress\Resources;

use JsonException;
use ZachGilbert\TransglobalExpress\Client;
use ZachGilbert\TransglobalExpress\Dto\BookDetails;
use ZachGilbert\TransglobalExpress\Dto\QuoteSelection;
use ZachGilbert\TransglobalExpress\Dto\Shipment as ShipmentDto;
use ZachGilbert\TransglobalExpress\Requests\BookShipmentRequest;
use ZachGilbert\TransglobalExpress\Responses\BookShipmentResponse;

class Shipment
{
    public function __construct(
        private readonly Client $client,
    ) {
    }

    /**
     * @param ShipmentDto|QuoteSelection $shipment
     * @param BookDetails                $bookDetails
     *
     * @return BookShipmentResponse
     * @throws JsonException
     */
    public function book(ShipmentDto|QuoteSelection $shipment, BookDetails $bookDetails): BookShipmentResponse
    {
        return BookShipmentResponse::fromArray(
            $this->client->send(new BookShipmentRequest($shipment, $bookDetails))
        );
    }
}
