<?php

namespace ZachGilbert\TransglobalExpress\Enums;

enum Environment: string {
    case Live = 'Live';
    case Staging = 'Staging';

    public function baseUri(): string
    {
        return match ($this) {
            self::Live => 'https://services3.transglobalexpress.co.uk',
            self::Staging => 'https://staging2.services3.transglobalexpress.co.uk',
        };
    }
}