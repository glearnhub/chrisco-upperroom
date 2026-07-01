<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'location',
        'start_datetime',
        'end_datetime',
        'image',
        'capacity',
        'registration_required',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'start_datetime' => 'datetime',
            'end_datetime' => 'datetime',
            'capacity' => 'integer',
            'registration_required' => 'boolean',
        ];
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function scopeUpcoming($query)
    {
        return $query->where(function ($q) {
            // Upcoming: hasn't started yet
            $q->where('status', 'upcoming')
              ->where('start_datetime', '>', now());
        })->orWhere(function ($q) {
            // Auto-ongoing: started but end_datetime hasn't passed (status still 'upcoming' in DB)
            $q->whereIn('status', ['upcoming', 'ongoing'])
              ->where('start_datetime', '<=', now())
              ->where('end_datetime', '>=', now());
        })->orWhere(function ($q) {
            // Explicitly ongoing
            $q->where('status', 'ongoing')
              ->where('end_datetime', '>=', now());
        })->orWhere(function ($q) {
            // Cancelled: show until scheduled start passes
            $q->where('status', 'cancelled')
              ->where('start_datetime', '>=', now());
        })->orderBy('start_datetime');
    }

    public function getDynamicStatusAttribute(): string
    {
        $now = now();
        if ($this->status === 'cancelled') {
            return 'cancelled';
        }
        if ($this->start_datetime <= $now && $this->end_datetime && $this->end_datetime >= $now) {
            return 'ongoing';
        }
        return 'upcoming';
    }

    public function isFull(): bool
    {
        if (!$this->capacity) {
            return false;
        }
        return $this->registrations()->where('status', '!=', 'cancelled')->count() >= $this->capacity;
    }
}
