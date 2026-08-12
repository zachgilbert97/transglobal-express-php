<?php

namespace ZachGilbert\TransglobalExpress\Requests;

use ZachGilbert\TransglobalExpress\Concerns\Request;
use ZachGilbert\TransglobalExpress\Dto\BookDetails;
use ZachGilbert\TransglobalExpress\Dto\QuoteSelection;
use ZachGilbert\TransglobalExpress\Dto\Shipment;

class BookShipmentRequest implements Request
{
    /**
     * @param Shipment|QuoteSelection $shipment A new shipment, optionally from a pre-made quote
     * @param BookDetails             $bookDetails
     */
    public function __construct(
        protected Shipment|QuoteSelection $shipment,
        protected BookDetails $bookDetails,
    ) {
    }

    public function method(): string
    {
        return 'POST';
    }

    public function uri(): string
    {
        return '/Book/V2/BookShipment';
    }

    /**
     * @return array
     */
    public function body(): array
    {
        return array_filter([
            $this->shipment instanceof QuoteSelection
                ? 'QuoteSelection'
                : 'Shipment' => $this->shipment->toArray(),
            'BookDetails' => $this->bookDetails->toArray(),
        ], static fn ($value): bool => !is_null($value));
    }
}
