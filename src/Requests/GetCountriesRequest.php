<?php

namespace ZachGilbert\TransglobalExpress\Requests;

use ZachGilbert\TransglobalExpress\Concerns\Request;

class GetCountriesRequest implements Request
{
    public function __construct()
    {}

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
        return '/Country/V2/GetCountries';
    }

    /**
     * @return array
     */
    public function body(): array
    {
        return [];
    }
}
