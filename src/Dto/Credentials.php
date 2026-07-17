<?php

namespace ZachGilbert\TransglobalExpress\Dto;

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;

final class Credentials implements Arrayable
{
    public function __construct(
        readonly public string $apiKey,
        readonly public string $password,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'APIKey' => $this->apiKey,
            'Password' => $this->password,
        ];
    }

    public static function fromArray(array $data): static
    {
        return new self(
            apiKey: $data['APIKey'],
            password: $data['Password'],
        );
    }
}