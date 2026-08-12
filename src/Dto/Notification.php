<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Notification implements Arrayable {
    /**
     * @param string|null $message
     * @param string|null $severity
     */
    public function __construct(
        public readonly ?string $message = null,
        public readonly ?string $severity = null,
    ) {}

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_filter([
            'Message' => $this->message,
            'Severity' => $this->severity,
        ], static fn ($value): bool => $value !== null);
    }

    /**
     * @param array $data
     *
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new self(
            message: $data['Message'] ?? null,
            severity: $data['Severity'] ?? null,
        );
    }
}
