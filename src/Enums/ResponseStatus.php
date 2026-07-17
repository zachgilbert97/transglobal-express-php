<?php

namespace ZachGilbert\TransglobalExpress\Enums;

enum ResponseStatus: string
{
    case SUCCESS = 'SUCCESS';
    case PARTIAL = 'PARTIAL';
    case FAIL = 'FAIL';
    case ERROR = 'ERROR';
}