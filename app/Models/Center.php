<?php

namespace App\Models;

use App\Enums\CenterStatus;
use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\CenterFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Center extends Model
{
    /** @use HasFactory<CenterFactory> */
    use HasFactory, HasLocalizedAttributes, SoftDeletes;

    protected $fillable = [
        'name_en',
        'name_fr',
        'slug',
        'city_en',
        'city_fr',
        'region_en',
        'region_fr',
        'address_en',
        'address_fr',
        'postal_address',
        'status',
        'is_headquarters',
        'booking_enabled',
        'description_en',
        'description_fr',
        'latitude',
        'longitude',
        'google_maps_url',
        'nearby_landmark',
        'search_keywords',
        'vehicle_categories_en',
        'vehicle_categories_fr',
        'featured_image',
        'target_opening_date',
        'target_date_text_en',
        'target_date_text_fr',
        'expansion_phase',
        'expansion_updated_at',
        'display_order',
        'is_featured',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'status' => CenterStatus::class,
            'is_headquarters' => 'boolean',
            'booking_enabled' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'target_opening_date' => 'date',
            'expansion_updated_at' => 'datetime',
            'display_order' => 'integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(CenterContact::class);
    }

    public function hours(): HasMany
    {
        return $this->hasMany(CenterHour::class);
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(CenterProgressUpdate::class);
    }

    public function services(): BelongsToMany
    {
        return $this->belongsToMany(Service::class)
            ->withPivot(['is_available', 'booking_enabled', 'note_en', 'note_fr', 'effective_date'])
            ->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function careerPosts(): HasMany
    {
        return $this->hasMany(CareerPost::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOperational(Builder $query): Builder
    {
        return $query->where('status', CenterStatus::ACTIVE->value);
    }

    public function scopeExpansion(Builder $query): Builder
    {
        return $query->whereIn('status', [
            CenterStatus::PLANNED->value,
            CenterStatus::CONSTRUCTION->value,
        ]);
    }

    public function scopeBookable(Builder $query): Builder
    {
        return $query->active()
            ->operational()
            ->where('booking_enabled', true);
    }
}
