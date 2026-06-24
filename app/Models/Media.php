<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory, HasLocalizedAttributes;

    protected $table = 'media';

    protected $fillable = [
        'uploaded_by',
        'file_name',
        'file_path',
        'file_type',
        'mime_type',
        'file_size',
        'alt_text_en',
        'alt_text_fr',
    ];

    protected function casts(): array
    {
        return [
            'file_size' => 'integer',
        ];
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
