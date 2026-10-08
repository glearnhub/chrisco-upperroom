<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceFollowup extends Model
{
    protected $fillable = [
        'user_id', 'year', 'month', 'reason', 'transferred_to', 'notes', 'recorded_by',
    ];

    public static $reasonLabels = [
        'transferred'   => 'Transferred to another Chrisco church',
        'left_church'   => 'Left church',
        'unwell'        => 'Unwell / Health issues',
        'job_related'   => 'Job related issues',
        'mission_field' => 'Was in mission field',
        'other'         => 'Other',
    ];

    public function member()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function recordedBy()
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }

    public function getReasonLabelAttribute(): string
    {
        return static::$reasonLabels[$this->reason] ?? $this->reason;
    }
}
