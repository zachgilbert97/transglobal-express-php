<?php

namespace ZachGilbert\TransglobalExpress\Concerns;

interface Request
{
    /**
     * @return string
     */
    public function method(): string;

    /**
     * @return string
     */
    public function uri(): string;

    /**
     * @return array
     */
    public function body(): array;
}
