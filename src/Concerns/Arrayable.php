<?php

namespace ZachGilbert\TransglobalExpress\Concerns;

interface Arrayable
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
