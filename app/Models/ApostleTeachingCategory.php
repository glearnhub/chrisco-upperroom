<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApostleTeachingCategory extends Model
{
    protected $fillable = ['name', 'slug', 'sort_order'];

    public function teachings()
    {
        return $this->hasMany(ApostleTeaching::class, 'category_id');
    }
}
