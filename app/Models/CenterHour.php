<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\CenterHourFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CenterHour extends Model
{
    /** @use HasFactory<CenterHourFactory> */
    use HasFactory, HasLocalizedAttributes;

    protected $fillable = [
        'center_id',
        'day_of_week',
        'opens_at',
        'closes_at',
        'is_closed',
        'special_note_en',
        'special_note_fr',
    ];

    protected function casts(): array
    {
        return [
            'is_closed' => 'boolean',
        ];
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }
}
