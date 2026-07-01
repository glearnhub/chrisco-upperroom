<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livestream extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'embed_url',
        'platform',
        'is_live',
        'scheduled_at',
    ];

    protected function casts(): array
    {
        return [
            'is_live' => 'boolean',
            'scheduled_at' => 'datetime',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('is_live', true);
    }
}
