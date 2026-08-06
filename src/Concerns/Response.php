<?php

namespace ZachGilbert\TransglobalExpress\Concerns;

interface Response
{
    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): static;
}
