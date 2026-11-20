<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'is_active',
        'is_admin',
        'is_auditor',
        'is_auditee',
        'fcm_token',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'is_active' => 'boolean',
            'is_admin' => 'boolean',
            'is_auditor' => 'boolean',
            'is_auditee' => 'boolean',
            'password' => 'hashed',
        ];
    }

    public function departments(): BelongsToMany
    {
        return $this->belongsToMany(Department::class, 'user_department');
    }

    public function findingsAsAuditor(): HasMany
    {
        return $this->hasMany(Finding::class, 'auditor_id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(FindingResponse::class, 'auditee_id');
    }

    public function verifications(): HasMany
    {
        return $this->hasMany(FindingVerification::class, 'auditor_id');
    }

    /**
     * True jika user punya kedua role sekaligus (perlu halaman pilih role).
     */
    public function hasMultipleRoles(): bool
    {
        return count($this->availableRoles()) > 1;
    }

    /**
     * Daftar role yang dimiliki user.
     *
     * @return array<int, string>
     */
    public function availableRoles(): array
    {
        $roles = [];

        if ($this->is_admin) {
            $roles[] = 'admin';
        }

        if ($this->is_auditor) {
            $roles[] = 'auditor';
        }

        if ($this->is_auditee) {
            $roles[] = 'auditee';
        }

        return $roles;
    }

    /**
     * Role default untuk user yang hanya punya satu role.
     */
    public function defaultRole(): ?string
    {
        return $this->availableRoles()[0] ?? null;
    }

    /**
     * Cek apakah user bertanggung jawab atas suatu department (sebagai auditee).
     */
    public function managesDepartment(int $departmentId): bool
    {
        return $this->departments()->where('departments.id', $departmentId)->exists();
    }
}
