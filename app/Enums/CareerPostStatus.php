<?php

namespace App\Enums;

enum CareerPostStatus: string
{
    case DRAFT = 'draft';
    case PUBLISHED = 'published';
    case CLOSING_SOON = 'closing_soon';
    case CLOSED = 'closed';
    case FILLED = 'filled';
    case ARCHIVED = 'archived';
}
