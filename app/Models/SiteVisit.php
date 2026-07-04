<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteVisit extends Model
{
    protected $fillable = ['path', 'ip', 'session_id', 'country', 'country_code', 'is_new_session'];
}
