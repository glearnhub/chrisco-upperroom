<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceSession extends Model
{
    protected $fillable = [
        'service_date', 'service_type', 'label', 'status',
        'opened_at', 'closed_at', 'opened_by', 'closed_by',
    ];

    protected function casts(): array
    {
        return [
            'service_date' => 'date',
            'opened_at'    => 'datetime',
            'closed_at'    => 'datetime',
        ];
    }

    public function attendances()
    {
        return $this->hasMany(ServiceAttendance::class, 'session_id');
    }

    public function openedBy()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function closedBy()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function attendanceCount(): int
    {
        return $this->attendances()->count();
    }

    public static function currentOpen(): ?self
    {
        return self::where('status', 'open')->latest('opened_at')->first();
    }

    public function getLabelDisplayAttribute(): string
    {
        if ($this->label) return $this->label;
        return match($this->service_type) {
            'sunday_morning'   => 'Sunday Morning Service',
            'sunday_afternoon' => 'Sunday Afternoon Service',
            'special'          => 'Special Service',
            default            => 'Service',
        };
    }
}
