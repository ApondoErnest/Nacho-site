<?php

namespace App\Models;

use App\Enums\CareerPostStatus;
use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\CareerPostFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CareerPost extends Model
{
    /** @use HasFactory<CareerPostFactory> */
    use HasFactory, HasLocalizedAttributes, SoftDeletes;

    protected $fillable = [
        'reference',
        'title_en',
        'title_fr',
        'slug',
        'department_id',
        'center_id',
        'employment_type',
        'summary_en',
        'summary_fr',
        'description_en',
        'description_fr',
        'responsibilities_en',
        'responsibilities_fr',
        'requirements_en',
        'requirements_fr',
        'preferred_requirements_en',
        'preferred_requirements_fr',
        'skills_en',
        'skills_fr',
        'application_documents_en',
        'application_documents_fr',
        'application_email',
        'application_subject',
        'application_instructions_en',
        'application_instructions_fr',
        'vacancies_count',
        'published_at',
        'closes_at',
        'status',
        'allow_email_application',
        'display_order',
        'created_by',
        'seo_title_en',
        'seo_title_fr',
        'meta_description_en',
        'meta_description_fr',
    ];

    protected function casts(): array
    {
        return [
            'vacancies_count' => 'integer',
            'published_at' => 'datetime',
            'closes_at' => 'date',
            'status' => CareerPostStatus::class,
            'allow_email_application' => 'boolean',
            'display_order' => 'integer',
        ];
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(CareerDepartment::class, 'department_id');
    }

    public function center(): BelongsTo
    {
        return $this->belongsTo(Center::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->whereIn('status', [
            CareerPostStatus::PUBLISHED->value,
            CareerPostStatus::CLOSING_SOON->value,
        ])->where(fn (Builder $query) => $query
            ->whereNull('published_at')
            ->orWhere('published_at', '<=', now()));
    }

    public function scopeOpen(Builder $query): Builder
    {
        return $query->published()
            ->where(fn (Builder $query) => $query
                ->whereNull('closes_at')
                ->orWhereDate('closes_at', '>=', today()));
    }
}
