<?php

namespace ZachGilbert\TransglobalExpress\Enums;

enum ServiceType: string
{
    case Air = 'Air';
    case Road = 'Road';
    case Air_And_Road = 'AirAndRoad';
}