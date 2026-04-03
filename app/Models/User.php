<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    const ROLES = [
        'super_admin'      => 'Super Admin',
        'facility_manager' => 'Facility Manager',
        'qa_manager'       => 'QA Manager',
        'study_director'   => 'Study Director',
        'project_manager'  => 'Project Manager',
        'archivist'        => 'Archivist',
        'read_only'        => 'Read Only',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Relationships ──────────────────────────────────────────────

    public function personnel(): HasOne
    {
        return $this->hasOne(Pro_Personnel::class, 'user_id', 'id');
    }

    // ── Role helpers ───────────────────────────────────────────────

    /** Check if user has one of the given role(s). */
    public function hasRole(string|array $roles): bool
    {
        return in_array($this->role, (array) $roles);
    }

    /** Super Admin only. */
    public function isAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    /** Can create / archive projects. */
    public function canCreateProject(): bool
    {
        return $this->hasRole(['super_admin', 'facility_manager', 'study_director']);
    }

    /** Can edit basic project information. */
    public function canEditProject(): bool
    {
        return $this->hasRole(['super_admin', 'facility_manager']);
    }

    /** Can write to Protocol Details, Planning, Protocol Dev, Report Phase. */
    public function canManageProtocol(): bool
    {
        return $this->hasRole(['super_admin', 'facility_manager', 'study_director', 'project_manager']);
    }

    /** Can create / edit / close QA inspections and findings. */
    public function canManageQA(): bool
    {
        return $this->hasRole(['super_admin', 'facility_manager', 'qa_manager']);
    }

    /** Can act on the Archiving Phase. */
    public function canManageArchiving(): bool
    {
        return $this->hasRole(['super_admin', 'facility_manager', 'archivist']);
    }

    /** Manage platform users (admin panel). */
    public function canManageUsers(): bool
    {
        return $this->role === 'super_admin';
    }

    /** Any write permission (used to hide generic action buttons for read_only). */
    public function canEdit(): bool
    {
        return $this->role !== 'read_only';
    }

    /** Human-readable label for current role. */
    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }
}
