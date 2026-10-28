<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

final class Credentials implements Arrayable, HasSerializableProperties
{
    use SerializesProperties;

    /**
     * @param string $apiKey
     * @param string $password
     */
    public function __construct(
        readonly public string $apiKey,
        readonly public string $password,
    )
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function properties(): array
    {
        return [
            'APIKey' => $this->apiKey,
            'Password' => $this->password,
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
            apiKey: $data['APIKey'],
            password: $data['Password'],
        );
    }
}
