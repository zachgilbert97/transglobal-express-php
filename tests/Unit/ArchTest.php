<?php

use ZachGilbert\TransglobalExpress\Concerns\Arrayable;
use ZachGilbert\TransglobalExpress\Concerns\HasSerializableProperties;
use ZachGilbert\TransglobalExpress\Traits\SerializesProperties;

arch('DTOs serialise through their declared properties')
    ->expect('ZachGilbert\TransglobalExpress\Dto')
    ->toBeClasses()
    ->toBeFinal()
    ->toImplement([Arrayable::class, HasSerializableProperties::class])
    ->toUseTrait(SerializesProperties::class);
