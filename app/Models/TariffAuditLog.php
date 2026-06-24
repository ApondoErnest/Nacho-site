<?php

namespace App\Models;

use Database\Factories\TariffAuditLogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TariffAuditLog extends Model
{
    /** @use HasFactory<TariffAuditLogFactory> */
    use HasFactory;

    public const UPDATED_AT = null;

    protected $fillable = [
        'tariff_id',
        'user_id',
        'changes',
    ];

    protected function casts(): array
    {
        return [
            'changes' => 'array',
        ];
    }

    public function tariff(): BelongsTo
    {
        return $this->belongsTo(Tariff::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
