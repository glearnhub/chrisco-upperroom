<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sermon extends Model
{
    use HasFactory;

    const CATEGORIES = [
        'sunday-service' => 'Sunday Service',
        'bible-hour'     => 'Bible Hour',
        'kesha'          => 'Radical Kesha',
        'proverbs-31'    => 'Proverbs 31',
        'handmaidens'    => 'Handmaidens',
        'others'         => 'Others',
    ];

    protected $fillable = [
        'title',
        'speaker',
        'description',
        'sermon_date',
        'category',
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
