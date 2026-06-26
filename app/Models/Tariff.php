<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\TariffFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

class Tariff extends Model
{
    /** @use HasFactory<TariffFactory> */
    use HasFactory, HasLocalizedAttributes;

    public const DEFAULT_VEHICLE_ICON = 'car-front';

    public const VEHICLE_ICONS = [
        'car-taxi-front',
        self::DEFAULT_VEHICLE_ICON,
        'truck',
        'bus-front',
        'bus',
        'tractor',
        'construction',
        'car',
        'van',
        'gauge',
        'banknote',
        'receipt',
    ];

    public const VALIDITY_UNITS = [
        'days',
        'months',
        'years',
    ];

    public const AUDITED_FIELDS = [
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

    /**
     * @return array<string, string>
     */
    public static function vehicleIconOptions(): array
    {
        return collect(self::VEHICLE_ICONS)
            ->mapWithKeys(fn (string $icon): array => [
                $icon => str($icon)->replace('-', ' ')->title()->toString(),
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    public static function validityUnitOptions(): array
    {
        return collect(self::VALIDITY_UNITS)
            ->mapWithKeys(fn (string $unit): array => [
                $unit => str($unit)->title()->toString(),
            ])
            ->all();
    }

    public function lucideIcon(): string
    {
        $icon = $this->vehicle_icon ?: self::DEFAULT_VEHICLE_ICON;

        return in_array($icon, self::VEHICLE_ICONS, true)
            ? $icon
            : self::DEFAULT_VEHICLE_ICON;
    }

    public function currentRevisionAsOf(mixed $date = null): ?TariffRevision
    {
        return $this->revisions()->currentAsOf($date)->first();
    }

    /**
     * @return array<string, mixed>
     */
    public function effectiveSnapshot(mixed $date = null): array
    {
        $base = Arr::only($this->attributesToArray(), self::AUDITED_FIELDS);
        $revision = $this->currentRevisionAsOf($date);

        if (! $revision) {
            return $base;
        }

        return [
            ...$base,
            ...Arr::only($revision->snapshot, self::AUDITED_FIELDS),
        ];
    }
}
