<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceAttendance extends Model
{
    protected $fillable = [
        'session_id', 'user_id', 'door', 'method', 'checked_in_at',
    ];

    protected function casts(): array
    {
        return ['checked_in_at' => 'datetime'];
    }

    public function session()
    {
        return $this->belongsTo(ServiceSession::class, 'session_id');
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
