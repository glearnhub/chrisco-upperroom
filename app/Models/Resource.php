<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Resource extends Model
{
    protected $fillable = ['title', 'author', 'description', 'cover_image', 'file_path', 'type', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->latest();
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'book'    => 'Book',
            'article' => 'Article',
            default   => ucfirst($this->type),
        };
    }
}
