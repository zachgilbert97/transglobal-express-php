<?php

namespace ZachGilbert\TransglobalExpress\Concerns;

interface HasSerializableProperties
{
    /**
     * API field names mapped to their raw values: scalars, enums, DTOs or lists of DTOs.
     *
     * Null values are omitted when serialised.
     *
     * @return array<string, mixed>
     */
    public function properties(): array;
}
