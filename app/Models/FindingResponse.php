<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FindingResponse extends Model
{
    use HasFactory;

    protected $fillable = [
        'finding_id',
        'auditee_id',
        'description_perbaikan',
        'status_baru',
        'responded_at',
    ];

    protected function casts(): array
    {
        return [
            'responded_at' => 'datetime',
        ];
    }

    public function finding(): BelongsTo
    {
        return $this->belongsTo(Finding::class);
    }

    public function auditee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditee_id');
    }
}
