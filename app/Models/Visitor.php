<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    protected $fillable = [
        'full_name', 'gender', 'residence', 'occupation', 'marital_status',
        'phone', 'email', 'preferred_contact',
        'visited_before', 'is_chrisco_member', 'chrisco_church',
        'attends_another_church', 'another_church_name',
        'visit_date', 'invited_by', 'how_heard',
        'prayer_request', 'follow_up_status', 'notes',
    ];

    protected $casts = [
        'preferred_contact'    => 'array',
        'visited_before'       => 'boolean',
        'is_chrisco_member'    => 'boolean',
        'attends_another_church' => 'boolean',
        'visit_date'           => 'date',
    ];
}
