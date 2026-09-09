<?php

namespace ZachGilbert\TransglobalExpress;

use ZachGilbert\TransglobalExpress\Enums\Environment;

final class Config
{
    public function __construct(
        public readonly string $apiKey,
        public readonly string $password,
        public readonly Environment $environment,
    ) {
    }

    public static function staging(string $apiKey, string $password): self
    {
        return new self($apiKey, $password, Environment::Staging);
    }

    public static function live(string $apiKey, string $password): self
    {
        return new self($apiKey, $password, Environment::Live);
    }

    public function baseUri(): string
    {
        return $this->environment->baseUri();
    }
}
