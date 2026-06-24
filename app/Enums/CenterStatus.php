<?php

namespace App\Enums;

enum CenterStatus: string
{
    case PLANNED = 'planned';
    case CONSTRUCTION = 'construction';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
}
