<?php

namespace ZachGilbert\TransglobalExpress\Enums;

enum LabelGenerateStatus: string
{
    case LabelGenerated = 'LabelGenerated';
    case Conditional = 'Conditional';
    case NoLabel = 'NoLabel';
}