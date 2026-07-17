<?php

namespace ZachGilbert\TransglobalExpress\Concerns;

interface Arrayable
{
    public function toArray(): array;

    public static function fromArray(array $data): static;
}