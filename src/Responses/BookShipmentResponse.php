<?php

namespace ZachGilbert\TransglobalExpress\Responses;

use ZachGilbert\TransglobalExpress\Concerns\Response;
use ZachGilbert\TransglobalExpress\Dto\BookShipment;

final class BookShipmentResponse implements Response
{
    public function __construct(
        private readonly BookShipment $bookShipment,
    ) {}

    public static function fromArray(array $data): static
    {
        return new self(
            bookShipment: BookShipment::fromArray($data),
        );
    }

    public function bookShipment(): BookShipment
    {
        return $this->bookShipment;
    }
}

// TODO - FailReference on response needs to be recorded
