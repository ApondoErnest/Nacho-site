<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\TariffFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tariff extends Model
{
    /** @use HasFactory<TariffFactory> */
    use HasFactory, HasLocalizedAttributes;

    protected $fillable = [
        'category_code',
        'category_slug',
        'name_en',
        'name_fr',
        'description_en',
        'description_fr',
        'price_fcfa',
        'validity_value',
        'validity_unit',
        'minimum_weight_kg',
        'maximum_weight_kg',
        'vehicle_icon',
        'effective_date',
        'expiry_date',
        'regulatory_reference',
        'last_verified_at',
        'is_active',
        'is_bookable',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'price_fcfa' => 'integer',
            'validity_value' => 'integer',
            'minimum_weight_kg' => 'integer',
            'maximum_weight_kg' => 'integer',
            'effective_date' => 'date',
            'expiry_date' => 'date',
            'last_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'is_bookable' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(TariffRevision::class);
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(TariffAuditLog::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeBookable(Builder $query): Builder
    {
        return $query->where('is_bookable', true);
    }

    public function scopeEffective(Builder $query, mixed $date = null): Builder
    {
        $date ??= today();

        return $query
            ->where(fn (Builder $query) => $query
                ->whereNull('effective_date')
                ->orWhereDate('effective_date', '<=', $date))
            ->where(fn (Builder $query) => $query
                ->whereNull('expiry_date')
                ->orWhereDate('expiry_date', '>=', $date));
    }
}
