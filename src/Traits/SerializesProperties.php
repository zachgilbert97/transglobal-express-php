<?php

namespace ZachGilbert\TransglobalExpress\Traits;

use BackedEnum;
use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;

/**
 * Serialises a DTO from a single map of API field names to values, omitting nulls.
 *
 * JSON serialisation always produces an object, so an empty DTO is sent as `{}`
 * rather than `[]`, while lists (e.g. Packages) remain JSON arrays.
 *
 * @phpstan-require-implements HasSerializableProperties
 */
trait SerializesProperties
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_map(
            static fn (mixed $value): mixed => self::normaliseForArray($value),
            $this->filledProperties(),
        );
    }

    /**
     * @return object
     */
    public function jsonSerialize(): object
    {
        return (object) $this->filledProperties();
    }

    /**
     * @return array<string, mixed>
     */
    private function filledProperties(): array
    {
        return array_filter($this->properties(), static fn (mixed $value): bool => !is_null($value));
    }

    /**
     * @param mixed $value
     *
     * @return mixed
     */
    private static function normaliseForArray(mixed $value): mixed
    {
        return match (true) {
            $value instanceof Arrayable => $value->toArray(),
            $value instanceof BackedEnum => $value->value,
            is_array($value) => array_map(static fn (mixed $item): mixed => self::normaliseForArray($item), $value),
            default => $value,
        };
    }
}
