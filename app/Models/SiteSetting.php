<?php

namespace App\Models;

use App\Enums\SettingType;
use Database\Factories\SiteSettingFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    /** @use HasFactory<SiteSettingFactory> */
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
    ];

    protected function casts(): array
    {
        return [
            'type' => SettingType::class,
        ];
    }

    public function scopeKey(Builder $query, string $key): Builder
    {
        return $query->where('key', $key);
    }

    public function typedValue(): mixed
    {
        return match ($this->type) {
            SettingType::BOOLEAN => filter_var($this->value, FILTER_VALIDATE_BOOLEAN),
            default => $this->value,
        };
    }
}
