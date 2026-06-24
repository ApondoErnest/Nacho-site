<?php

namespace App\Enums;

enum SettingType: string
{
    case TEXT = 'text';
    case BOOLEAN = 'boolean';
    case IMAGE = 'image';
    case COLOR = 'color';
}
