<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\CareerDepartmentFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CareerDepartment extends Model
{
    /** @use HasFactory<CareerDepartmentFactory> */
    use HasFactory, HasLocalizedAttributes;

    public const DEFAULT_ICON = 'briefcase-business';

    public const LUCIDE_ICONS = [
        'badge-check',
        'briefcase-business',
        'building-2',
        'car-front',
        'clipboard-check',
        'file-text',
        'graduation-cap',
        'monitor-cog',
        'settings',
        'shield-check',
        'user-check',
        'users',
        'wrench',
    ];

    protected $fillable = [
        'name_en',
        'name_fr',
        'slug',
        'description_en',
        'description_fr',
        'icon',
        'display_order',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'display_order' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function posts(): HasMany
    {
        return $this->hasMany(CareerPost::class, 'department_id');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @return array<string, string>
     */
    public static function iconOptions(): array
    {
        return collect(self::LUCIDE_ICONS)
            ->mapWithKeys(fn (string $icon): array => [$icon => str($icon)->replace('-', ' ')->title()->toString()])
            ->all();
    }

    public function lucideIcon(): string
    {
        return in_array($this->icon, self::LUCIDE_ICONS, true)
            ? $this->icon
            : self::DEFAULT_ICON;
    }
}
