<?php

namespace ZachGilbert\TransglobalExpress\Resources;

use JsonException;
use ZachGilbert\TransglobalExpress\Client;
use ZachGilbert\TransglobalExpress\Requests\GetCountriesRequest;
use ZachGilbert\TransglobalExpress\Responses\GetCountriesResponse;

class Countries
{
    /**
     * @param Client $client
     */
    public function __construct(
        private readonly Client $client,
    ) {
    }

    /**
     * @return GetCountriesResponse
     * @throws JsonException
     */
    public function get(): GetCountriesResponse
    {
        return GetCountriesResponse::fromArray(
            $this->client->send(new GetCountriesRequest)
        );
    }
}
