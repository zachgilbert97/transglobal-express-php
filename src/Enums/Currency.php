<?php

namespace ZachGilbert\TransglobalExpress\Enums;

enum Currency: string
{
    case GBP = 'GBP'; // Great British Pound
    case EUR = 'EUR'; // Euro
    case USD = 'USD'; // US Dollar
    case AUD = 'AUD'; // Australian Dollar
    case NZD = 'NZD'; // New Zealand Dollar
    case CAD = 'CAD'; // Canadian Dollar
    case CHF = 'CHF'; // Swiss Franc
    case CNY = 'CNY'; // Chinese Renminbi
    case SEK = 'SEK'; // Swedish Krona
    case HKD = 'HKD'; // Hong Kong Dollar
    case AED = 'AED'; // UAE Dirham
    case JPY = 'JPY'; // Japanese Yen
}