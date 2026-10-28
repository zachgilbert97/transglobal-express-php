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
     * The top-level request fields, encoded with json_encode().
     *
     * Pass DTOs as-is rather than calling toArray(), so empty DTOs are sent as `{}`.
     *
     * @return array<string, mixed>
     */
    public function body(): array;
}
