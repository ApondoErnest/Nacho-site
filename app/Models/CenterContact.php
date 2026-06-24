<?php

namespace App\Models;

use App\Enums\ContactType;
use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\CenterContactFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CenterContact extends Model
{
    /** @use HasFactory<CenterContactFactory> */
    use HasFactory, HasLocalizedAttributes;

    protected $fillable = [
        'center_id',
        'type',
        'label_en',
        'label_fr',
        'value',
        'is_primary',
        'is_public',
        'display_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => ContactType::class,
            'is_primary' => 'boolean',
            'is_public' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function scopePublic(Builder $query): Builder
    {
        return $query->where('is_public', true);
    }

    public function scopePrimary(Builder $query): Builder
    {
        return $query->where('is_primary', true);
    }

    public function scopeType(Builder $query, ContactType|string $type): Builder
    {
        return $query->where('type', $type instanceof ContactType ? $type->value : $type);
    }
}
