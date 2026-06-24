<?php

namespace App\Models;

use App\Enums\ContactMessageStatus;
use Database\Factories\ContactMessageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    /** @use HasFactory<ContactMessageFactory> */
    use HasFactory;

    protected $fillable = [
        'full_name',
        'email',
        'phone',
        'subject',
        'message',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContactMessageStatus::class,
        ];
    }

    public function scopeStatus(Builder $query, ContactMessageStatus|string $status): Builder
    {
        return $query->where('status', $status instanceof ContactMessageStatus ? $status->value : $status);
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->whereIn('status', [
            ContactMessageStatus::NEW->value,
            ContactMessageStatus::READ->value,
        ]);
    }
}
