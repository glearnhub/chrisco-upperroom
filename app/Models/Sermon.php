<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sermon extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'speaker',
        'description',
        'sermon_date',
        'series',
        'scripture',
        'audio_url',
        'video_url',
        'thumbnail',
        'status',
        'views',
    ];

    protected function casts(): array
    {
        return [
            'sermon_date' => 'date',
            'views' => 'integer',
        ];
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function incrementViews(): void
    {
        $this->increment('views');
    }
}
