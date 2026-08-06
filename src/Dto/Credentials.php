<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Credentials implements Arrayable
{
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
     * @return array
     */
    public function toArray(): array
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
