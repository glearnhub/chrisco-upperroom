<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'middle_name', 'last_name', 'gender', 'marital_status',
        'email', 'password', 'role',
        'phone', 'address', 'date_of_birth', 'profile_photo', 'membership_date',
        'county', 'sub_county', 'sub_location',
        'salvation_date', 'is_committed_member', 'committed_date',
        'department', 'department2', 'department3', 'occupation',
        'next_of_kin_name', 'next_of_kin_relationship', 'next_of_kin_phone',
        'belongs_to_home_cell', 'home_cell',
        'assigned_to_deacon', 'deacon_name',
        'office',
    ];

    public function getFullNameAttribute(): string
    {
        return trim($this->name . ' ' . ($this->middle_name ?? '') . ' ' . ($this->last_name ?? ''));
    }

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'password'             => 'hashed',
            'date_of_birth'        => 'date',
            'membership_date'      => 'date',
            'belongs_to_home_cell'  => 'boolean',
            'assigned_to_deacon'    => 'boolean',
            'is_committed_member'   => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function donations()
    {
        return $this->hasMany(Donation::class);
    }

    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function prayerRequests()
    {
        return $this->hasMany(PrayerRequest::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }
}
