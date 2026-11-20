<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FindingPhoto extends Model
{
    use HasFactory;

    public const TYPE_TEMUAN = 'temuan';
    public const TYPE_PERBAIKAN = 'perbaikan';

    protected $fillable = [
        'finding_id',
        'type',
        'path',
        'taken_at',
        'file_hash',
        'uploaded_by',
    ];

    protected function casts(): array
    {
        return [
            'taken_at' => 'datetime',
        ];
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
