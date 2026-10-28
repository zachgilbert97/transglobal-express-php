<?php

namespace ZachGilbert\TransglobalExpress\Concerns;

use JsonSerializable;

interface Arrayable extends JsonSerializable
{
    /**
     * @return array
     */
    public function toArray(): array;

    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): static;
}
