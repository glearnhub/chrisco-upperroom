<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChildAttendance extends Model
{
    protected $fillable = [
        'child_id', 'attendance_date', 'method', 'confidence', 'marked_by',
    ];

    protected function casts(): array
    {
        return ['attendance_date' => 'date'];
    }

    public function child()
    {
        return $this->belongsTo(Child::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(User::class, 'marked_by');
    }
}
