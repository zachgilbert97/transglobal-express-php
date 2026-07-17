<?php

namespace ZachGilbert\TransglobalExpress\Enums;

enum ItemType: string
{
    case Parcel = 'Parcel';
    case Document = 'Document';
    case Pallet = 'Pallet';
    case Suitcase = 'Suitcase';
}