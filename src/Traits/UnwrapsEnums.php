<?php

namespace ZachGilbert\TransglobalExpress\Traits;

use BackedEnum;

trait UnwrapsEnums
{
    /**
     * @param BackedEnum|int|string|null $value
     *
     * @return int|string|null
     */
    private function unwrapEnumValue(BackedEnum|int|string|null $value): int|string|null
    {
        return $value instanceof BackedEnum
            ? $value->value
            : $value;
    }
}