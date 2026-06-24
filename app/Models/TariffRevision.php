<?php

namespace App\Models;

use App\Enums\TariffRevisionStatus;
use Database\Factories\TariffRevisionFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffRevision extends Model
{
    /** @use HasFactory<TariffRevisionFactory> */
    use HasFactory;

    protected $fillable = [
        'tariff_id',
        'created_by',
        'snapshot',
        'effective_date',
        'published_at',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'snapshot' => 'array',
            'effective_date' => 'date',
            'published_at' => 'datetime',
            'status' => TariffRevisionStatus::class,
        ];
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeCurrentAsOf(Builder $query, mixed $date = null): Builder
    {
        $date ??= today();

        return $query
            ->whereIn('status', [
                TariffRevisionStatus::ACTIVE->value,
                TariffRevisionStatus::SCHEDULED->value,
            ])
            ->whereDate('effective_date', '<=', $date)
            ->latest('effective_date');
    }
}
