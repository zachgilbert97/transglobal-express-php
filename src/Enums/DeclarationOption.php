<?php

namespace ZachGilbert\TransglobalExpress\Enums;

enum DeclarationOption: int
{
    case Full_Certificate_Of_Conformity = 1; // Not available for USPS services
    case CPSC_Product_Registry = 2;
    case Exempt_or_Disclaimer = 3;
}