<?php

namespace App\Enums;

enum ContactMessageStatus: string
{
    case NEW = 'new';
    case READ = 'read';
    case REPLIED = 'replied';
    case ARCHIVED = 'archived';
}
