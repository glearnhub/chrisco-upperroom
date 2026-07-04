<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRegistration extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'user_id',
        'member_id',
        'full_name',
        'phone',
        'email',
        'category',
        'status',
        'attended',
        'attended_at',
    ];

    protected $casts = [
        'attended'    => 'boolean',
        'attended_at' => 'datetime',
    ];

    const CATEGORIES = ['presbyter','pastor','elder','deacon','deaconess','member','visitor'];

    const CATEGORY_ORDER = [
        'presbyter' => 1,
        'pastor'    => 2,
        'elder'     => 3,
        'deacon'    => 4,
        'deaconess' => 5,
        'member'    => 6,
        'visitor'   => 7,
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'member_id');
    }
}
