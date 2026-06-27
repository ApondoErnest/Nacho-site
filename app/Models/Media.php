<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedAttributes;
use Database\Factories\MediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    /** @use HasFactory<MediaFactory> */
    use HasFactory, HasLocalizedAttributes;

    public const TYPE_IMAGE = 'image';

    public const TYPE_DOCUMENT = 'document';

    public const ALLOWED_EXTENSIONS = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx'];

    public const MAX_UPLOAD_KILOBYTES = 10_240;

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

    public function isImage(): bool
    {
        return $this->file_type === self::TYPE_IMAGE;
    }

    public function isDocument(): bool
    {
        return $this->file_type === self::TYPE_DOCUMENT;
    }

    public function publicUrl(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    public function humanFileSize(): string
    {
        if (! $this->file_size) {
            return 'Unknown';
        }

        if ($this->file_size >= 1_048_576) {
            return number_format($this->file_size / 1_048_576, 1).' MB';
        }

        return number_format($this->file_size / 1024, 1).' KB';
    }
}
