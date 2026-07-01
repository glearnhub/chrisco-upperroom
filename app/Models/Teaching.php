<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teaching extends Model
{
    protected $fillable = ['title', 'pastor_name', 'image', 'content', 'category', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->latest();
    }
}
