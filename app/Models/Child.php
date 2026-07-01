<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Child extends Model
{
    protected $fillable = [
        'first_name', 'middle_name', 'last_name',
        'date_of_birth', 'gender',
        'parent1_id', 'parent1_name', 'parent1_contact',
        'parent2_id', 'parent2_name', 'parent2_contact',
        'sunday_school_class', 'notes',
    ];

    protected function casts(): array
    {
        return ['date_of_birth' => 'date'];
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->first_name . ' ' . ($this->middle_name ?? '') . ' ' . $this->last_name);
    }

    public function parent1()
    {
        return $this->belongsTo(User::class, 'parent1_id');
    }

    public function parent2()
    {
        return $this->belongsTo(User::class, 'parent2_id');
    }

    public function getParent1DisplayAttribute(): string
    {
        return $this->parent1?->name ?? $this->parent1_name ?? '—';
    }

    public function getParent2DisplayAttribute(): string
    {
        return $this->parent2?->name ?? $this->parent2_name ?? '—';
    }

    public static function sundaySchoolClasses(): array
    {
        return [
            'Nursery'      => 'Nursery (0–2 yrs)',
            'Toddlers'     => 'Toddlers (3–4 yrs)',
            'Beginners'    => 'Beginners (5–6 yrs)',
            'Primary'      => 'Primary (7–9 yrs)',
            'Junior'       => 'Junior (10–12 yrs)',
            'Intermediate' => 'Intermediate (13–14 yrs)',
            'Teen'         => 'Teen / Senior (15–17 yrs)',
        ];
    }
}
