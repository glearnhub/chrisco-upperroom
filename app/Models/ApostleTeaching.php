<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApostleTeaching extends Model
{
    protected $fillable = [
        'title', 'description', 'youtube_url', 'thumbnail',
        'category_id', 'status', 'views',
    ];

    public function category()
    {
        return $this->belongsTo(ApostleTeachingCategory::class, 'category_id');
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }

    public function getEmbedUrlAttribute(): string
    {
        $url = $this->youtube_url;
        if (preg_match('/(?:youtube\.com\/watch\?v=|youtu\.be\/|youtube\.com\/live\/)([a-zA-Z0-9_-]{11})/', $url, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1] . '?rel=0';
        }
        return $url;
    }
}
