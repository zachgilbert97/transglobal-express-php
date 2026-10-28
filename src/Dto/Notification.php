<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class Notification implements Arrayable, HasSerializableProperties {
    use SerializesProperties;

    /**
     * @param string|null $message
     * @param string|null $severity
     */
    public function __construct(
        public readonly ?string $message = null,
        public readonly ?string $severity = null,
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'Message' => $this->message,
            'Severity' => $this->severity,
        ];
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
