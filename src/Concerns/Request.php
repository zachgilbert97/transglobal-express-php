<?php

namespace ZachGilbert\TransglobalExpress\Concerns;

interface Request
{
    public function method(): string;

    public function uri(): string;

    public function body(): array;
}