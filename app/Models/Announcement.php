<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    const CATEGORIES = [
        'general' => 'General',
        'events'  => 'Events',
        'youth'   => 'Youth',
        'women'   => 'Women',
        'men'     => 'Men',
        'prayer'  => 'Prayer',
        'finance' => 'Finance',
    ];

    const CATEGORY_COLORS = [
        'general' => '#0a1f44',
        'events'  => '#c0392b',
        'youth'   => '#7c3aed',
        'women'   => '#db2777',
        'men'     => '#1d4ed8',
        'prayer'  => '#065f46',
        'finance' => '#92400e',
    ];

    protected $fillable = [
        'title',
        'category',
        'body',
        'image',
        'is_published',
        'is_pinned',
        'is_recurring',
        'expires_at',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_published'  => 'boolean',
            'is_pinned'     => 'boolean',
            'is_recurring'  => 'boolean',
            'expires_at'    => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_published', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at');
    }
}
