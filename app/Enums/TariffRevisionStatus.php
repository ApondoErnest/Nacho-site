<?php

namespace App\Enums;

enum TariffRevisionStatus: string
{
    case SCHEDULED = 'scheduled';
    case ACTIVE = 'active';
    case SUPERSEDED = 'superseded';
    case CANCELLED = 'cancelled';
}
