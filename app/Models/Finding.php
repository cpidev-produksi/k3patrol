<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

class Finding extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'Open';
    public const STATUS_ON_PROCESS = 'On Process';
    public const STATUS_CLOSED = 'Closed';
    public const STATUS_CLOSED_VERIFIED = 'Closed Verified';

    public const STATUS_FLOW = [
        self::STATUS_OPEN => [self::STATUS_ON_PROCESS, self::STATUS_CLOSED],
        self::STATUS_ON_PROCESS => [self::STATUS_CLOSED],
        self::STATUS_CLOSED => [self::STATUS_ON_PROCESS, self::STATUS_CLOSED_VERIFIED],
        self::STATUS_CLOSED_VERIFIED => [],
    ];

    public const RISK_SLA_HOURS = [
        'Extreme' => 24,
        'High' => 24 * 7,
        'Medium' => 24 * 30,
        'Low' => 24 * 90,
    ];

    protected $fillable = [
        'finding_number',
        'auditor_id',
        'department_id',
        'category_id',
        'category_other',
        'description',
        'area_detail',
        'risk_level',
        'due_at',
        'status',
        'reopen_note',
        'reopened_at',
        'reopened_by',
        'near_deadline_notified_at',
        'overdue_notified_at',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'reopened_at' => 'datetime',
            'near_deadline_notified_at' => 'datetime',
            'overdue_notified_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Finding $finding) {
            if (empty($finding->finding_number)) {
                $finding->finding_number = self::generateNumber();
            }
            if (empty($finding->status)) {
                $finding->status = self::STATUS_OPEN;
            }
            if (empty($finding->due_at)) {
                $finding->due_at = now()->addHours(self::slaHoursFor($finding->risk_level));
            }
        });
    }

    public static function slaHoursFor(string $riskLevel): int
    {
        return self::RISK_SLA_HOURS[$riskLevel] ?? 24;
    }

    public static function generateNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->year;
            $prefix = "K3-{$year}-";

            $last = self::where('finding_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->orderByDesc('finding_number')
                ->first();

            $nextSeq = 1;
            if ($last) {
                $lastSeq = (int) substr($last->finding_number, strlen($prefix));
                $nextSeq = $lastSeq + 1;
            }

            return $prefix . str_pad($nextSeq, 5, '0', STR_PAD_LEFT);
        });
    }

    public function canTransitionTo(string $newStatus): bool
    {
        return in_array($newStatus, self::STATUS_FLOW[$this->status] ?? [], true);
    }

    public function isOverdue(): bool
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_ON_PROCESS], true)
            && $this->due_at->isPast();
    }

    /*badge "mendekati deadline" (belum lewat, tapi tinggal sedikit lagi). */
    public function isNearDeadline(int $hoursThreshold = 6): bool
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_ON_PROCESS], true)
            && !$this->isOverdue()
            && $this->due_at->lessThanOrEqualTo(now()->addHours($hoursThreshold));
    }

    // ---------- Relationships ----------

    public function auditor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'auditor_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function photos(): HasMany
    {
        return $this->hasMany(FindingPhoto::class);
    }

    public function response(): HasOne
    {
        return $this->hasOne(FindingResponse::class)->latestOfMany();
    }

    public function responses(): HasMany
    {
        return $this->hasMany(FindingResponse::class);
    }

    public function verification(): HasOne
    {
        return $this->hasOne(FindingVerification::class)->latestOfMany('verified_at');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(FindingVerification::class);
    }

    public function reopenedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reopened_by');
    }

    // ---------- Scopes ----------

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_ON_PROCESS])
            ->where('due_at', '<', now());
    }

    public function scopeNearDeadline(Builder $query, int $hoursThreshold = 6): Builder
    {
        return $query->whereIn('status', [self::STATUS_OPEN, self::STATUS_ON_PROCESS])
            ->whereBetween('due_at', [now(), now()->addHours($hoursThreshold)]);
    }

    public function scopeForDepartments(Builder $query, array $departmentIds): Builder
    {
        return $query->whereIn('department_id', $departmentIds);
    }

    public function scopeFilter(Builder $query, array $filters): Builder
    {
        return $query
            ->when($filters['status'] ?? null, fn ($q, $v) => $q->where('status', $v))
            ->when($filters['risk_level'] ?? null, fn ($q, $v) => $q->where('risk_level', $v))
            ->when($filters['department_id'] ?? null, fn ($q, $v) => $q->where('department_id', $v))
            ->when($filters['category_id'] ?? null, fn ($q, $v) => $q->where('category_id', $v))
            ->when($filters['date_from'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
            ->when($filters['date_to'] ?? null, fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
    }
}