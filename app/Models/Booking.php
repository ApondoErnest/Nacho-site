<?php

namespace App\Models;

use App\Enums\BookingStatus;
use Database\Factories\BookingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    /** @use HasFactory<BookingFactory> */
    use HasFactory;

    protected $fillable = [
        'booking_reference',
        'full_name',
        'phone',
        'email',
        'center_id',
        'service_id',
        'tariff_id',
        'vehicle_registration',
        'preferred_date',
        'preferred_time',
        'document_path',
        'comment',
        'consent',
        'status',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'preferred_date' => 'date',
            'consent' => 'boolean',
            'status' => BookingStatus::class,
        ];
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function scopeStatus(Builder $query, BookingStatus|string $status): Builder
    {
        return $query->where('status', $status instanceof BookingStatus ? $status->value : $status);
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->status(BookingStatus::PENDING);
    }
}
