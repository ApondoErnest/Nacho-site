<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\ServiceFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    /** @use HasFactory<ServiceFactory> */
    use HasFactory, HasLocalizedAttributes, SoftDeletes;

    protected $fillable = [
        'slug',
        'title_en',
        'title_fr',
        'short_description_en',
        'short_description_fr',
        'full_description_en',
        'full_description_fr',
        'icon',
        'featured_image',
        'is_active',
        'display_order',
        'seo_title_en',
        'seo_title_fr',
        'meta_description_en',
        'meta_description_fr',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function centers(): BelongsToMany
    {
        return $this->belongsToMany(Center::class)
            ->withPivot(['is_available', 'booking_enabled', 'note_en', 'note_fr', 'effective_date'])
            ->withTimestamps();
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }
}
