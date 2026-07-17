<?php

namespace ZachGilbert\TransglobalExpress\Concerns;

interface Response
{
    public static function fromArray(array $data): static;
}