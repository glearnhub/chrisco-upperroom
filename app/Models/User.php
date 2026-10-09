<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'middle_name', 'last_name', 'gender', 'marital_status',
        'email', 'password', 'member_type', 'is_active',
        'phone', 'address', 'date_of_birth', 'profile_photo', 'membership_date',
        'county', 'sub_county', 'sub_location',
        'salvation_date', 'is_born_again', 'is_baptized', 'baptism_date',
        'is_committed_member', 'in_commitment_class', 'committed_date',
        'department', 'department2', 'department3', 'occupation',
        'next_of_kin_name', 'next_of_kin_relationship', 'next_of_kin_phone',
        'next_of_kin2_name', 'next_of_kin2_relationship', 'next_of_kin2_phone',
        'belongs_to_home_cell', 'home_cell',
        'assigned_to_deacon', 'deacon_name',
        'office',
        'transfer_type', 'transfer_church',
        'has_medical_condition', 'medical_conditions', 'medications',
        'allergies', 'emergency_medical_contact', 'emergency_medical_phone', 'special_needs',
        'must_change_password',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'date_of_birth'        => 'date',
            'membership_date'      => 'date',
            'belongs_to_home_cell' => 'boolean',
            'assigned_to_deacon'   => 'boolean',
            'is_born_again'        => 'boolean',
            'is_baptized'          => 'boolean',
            'is_committed_member'  => 'boolean',
            'in_commitment_class'  => 'boolean',
            'is_active'              => 'boolean',
            'has_medical_condition'  => 'boolean',
            'must_change_password'   => 'boolean',
        ];
    }

    // ── RBAC Relationships ──────────────────────────────────────────────────

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    public function userPermissions()
    {
        return $this->hasMany(UserPermission::class);
    }

    // ── Permission Checking ─────────────────────────────────────────────────

    public function isSuperAdmin(): bool
    {
        if (!$this->isAdmin()) return false;
        return $this->roles()->where('is_super_admin', true)->exists();
    }

    /**
     * Effective permissions = role permissions + individual grants − individual revokes
     */
    public function hasPermission(string $slug): bool
    {
        if ($this->isSuperAdmin()) return true;
        if (!$this->isAdmin()) return false;

        // Individual override takes priority
        $override = $this->userPermissions()
            ->whereHas('permission', fn($q) => $q->where('slug', $slug))
            ->first();

        if ($override) {
            return $override->type === 'grant';
        }

        // Fall back to role permissions
        return $this->roles()
            ->whereHas('permissions', fn($q) => $q->where('slug', $slug))
            ->exists();
    }

    public function getEffectivePermissions(): \Illuminate\Support\Collection
    {
        if ($this->isSuperAdmin()) {
            return Permission::all()->pluck('slug');
        }

        // Start with all role permission slugs
        $rolePerms = $this->roles()
            ->with('permissions')
            ->get()
            ->flatMap(fn($r) => $r->permissions->pluck('slug'))
            ->unique();

        // Apply individual overrides
        $overrides = $this->userPermissions()->with('permission')->get();
        $grants  = $overrides->where('type', 'grant')->pluck('permission.slug');
        $revokes = $overrides->where('type', 'revoke')->pluck('permission.slug');

        return $rolePerms->merge($grants)->diff($revokes)->unique()->values();
    }

    // ── Helpers ─────────────────────────────────────────────────────────────

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . ($this->middle_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // ── Existing Relationships ───────────────────────────────────────────────

    public function donations()           { return $this->hasMany(Donation::class); }
    public function eventRegistrations()  { return $this->hasMany(EventRegistration::class); }
    public function prayerRequests()      { return $this->hasMany(PrayerRequest::class); }
    public function announcements()       { return $this->hasMany(Announcement::class); }
    public function serviceAttendances()  { return $this->hasMany(ServiceAttendance::class); }
}
