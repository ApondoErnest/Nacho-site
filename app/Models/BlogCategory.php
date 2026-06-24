<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\BlogCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BlogCategory extends Model
{
    /** @use HasFactory<BlogCategoryFactory> */
    use HasFactory, HasLocalizedAttributes;

    protected $fillable = [
        'name_en',
        'name_fr',
        'slug',
        'description_en',
        'description_fr',
    ];

    public function posts(): HasMany
    {
        return $this->hasMany(BlogPost::class);
    }
}
