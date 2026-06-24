<?php

namespace App\Models;

use App\Enums\ContentStatus;
use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\PageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Page extends Model
{
    /** @use HasFactory<PageFactory> */
    use HasFactory, HasLocalizedAttributes, SoftDeletes;

    protected $fillable = [
        'title_en',
        'title_fr',
        'slug',
        'content_en',
        'content_fr',
        'status',
        'seo_title_en',
        'seo_title_fr',
        'meta_description_en',
        'meta_description_fr',
    ];

    protected function casts(): array
    {
        return [
            'status' => ContentStatus::class,
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', ContentStatus::PUBLISHED->value);
    }
}
