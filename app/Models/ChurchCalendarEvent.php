<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ChurchCalendarEvent extends Model
{
    protected $fillable = [
        'title', 'start_date', 'end_date', 'category', 'color',
        'notes', 'calendar_year', 'is_published',
    ];

    protected function casts(): array
    {
        return [
            'start_date'   => 'date',
            'end_date'     => 'date',
            'is_published' => 'boolean',
        ];
    }

    // All dates this event spans (for calendar rendering)
    public function getDatesAttribute(): array
    {
        $start = $this->start_date;
        $end   = $this->end_date ?? $this->start_date;
        $dates = [];
        $cur   = $start->copy();
        while ($cur->lte($end)) {
            $dates[] = $cur->toDateString();
            $cur->addDay();
        }
        return $dates;
    }

    public function isMultiDay(): bool
    {
        return $this->end_date && !$this->end_date->eq($this->start_date);
    }

    public static function categories(): array
    {
        return [
            'general'   => ['label' => 'General',          'color' => '#1e3a6e'],
            'special'   => ['label' => 'Special Service',  'color' => '#c0392b'],
            'campus'    => ['label' => 'Campus/BR Meeting','color' => '#0e7490'],
            'outreach'  => ['label' => 'Outreach/Mission', 'color' => '#1e3a6e'],
            'women'     => ['label' => "Women's Ministry", 'color' => '#c0392b'],
            'men'       => ['label' => "Men's Ministry",   'color' => '#c0392b'],
            'youth'     => ['label' => 'Youth',            'color' => '#1e3a6e'],
            'prayer'    => ['label' => 'Prayer',           'color' => '#c0392b'],
            'other'     => ['label' => 'Other',            'color' => '#555555'],
        ];
    }
}
