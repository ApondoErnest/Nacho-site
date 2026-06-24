<?php

namespace App\Enums;

enum ContactType: string
{
    case PHONE = 'phone';
    case WHATSAPP = 'whatsapp';
    case EMAIL = 'email';
}
