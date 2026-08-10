<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChurchLeader extends Model
{
    protected $fillable = ['name', 'title', 'role', 'bio', 'photo', 'sort_order', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function scopeActive($q) { return $q->where('is_active', true)->orderBy('sort_order'); }

    public function getRoleLabelAttribute(): string
    {
        return $this->role ?: 'Church Leader';
    }
}
